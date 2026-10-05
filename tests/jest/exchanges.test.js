const { apiCall, loginAs } = require('./helpers');

async function firstAvailableProduct(cookies) {
    const res = await apiCall('/products/index.php?per_page=30&status=active', 'GET', null, cookies);
    if (!res.data.success) throw new Error('product list failed: ' + JSON.stringify(res.data));
    const product = res.data.data.products.find((p) => Number(p.total_stock) > 0);
    if (!product) throw new Error('no product with stock available for exchange test');
    return product;
}

async function createReturnOnSale(cookies, saleId) {
    const rb = await apiCall(`/returns/returnable.php?sale_id=${saleId}`, 'GET', null, cookies);
    if (!rb.data.success || !rb.data.data.items) throw new Error('returnable failed: ' + JSON.stringify(rb.data));
    const item = rb.data.data.items.find((i) => Number(i.returnable_qty) > 0);
    if (!item) throw new Error('no returnable line on sale ' + saleId);

    const created = await apiCall('/returns/index.php', 'POST', {
        type: 'pos',
        sale_id: saleId,
        items: [{ sale_item_id: item.id, qty: item.returnable_qty }],
        reason_code: 'defective',
    }, cookies);
    if (!created.data.success) throw new Error('return create failed: ' + JSON.stringify(created.data));
    const detail = await apiCall(`/returns/index.php?id=${created.data.data.id}`, 'GET', null, cookies);
    return { returnId: created.data.data.id, returnItemId: detail.data.data.items[0].id };
}

describe('POS Exchange (Phase 15)', () => {
    let cookies;
    let sourceProduct;
    let saleId;
    let returnId;
    let returnItemId;

    beforeAll(async () => {
        cookies = (await loginAs()).cookies;
        sourceProduct = await firstAvailableProduct(cookies);
    });

    test('sale -> return -> approve -> receive -> exchange (zero difference)', async () => {
        const sale = await apiCall('/sales/index.php', 'POST', {
            customer_id: null,
            items: [{ product_id: sourceProduct.id, qty: 1, unit_price: sourceProduct.price }],
            discount: 0,
            shipping: 0,
            payment_method: 'cash',
        }, cookies);
        expect(sale.data.success).toBe(true);
        saleId = sale.data.data.id;

        const created = await createReturnOnSale(cookies, saleId);
        returnId = created.returnId;
        returnItemId = created.returnItemId;

        // POS returns auto-approve at creation (Phase 13), so approve() must refuse.
        const approve = await apiCall('/returns/approve.php', 'POST', { return_id: returnId, action: 'approve', note: '' }, cookies);
        expect(approve.data.success).toBe(false);

        const receive = await apiCall('/returns/receive.php', 'POST', {
            return_id: returnId,
            items: [{ return_item_id: returnItemId, restock: 1, condition_note: '' }],
        }, cookies);
        expect(receive.data.success).toBe(true);

        // Like-for-like swap: replacement equals the credit, so no payment owed.
        const exchange = await apiCall('/returns/exchange.php', 'POST', {
            return_id: returnId,
            items: [],
            replacement_items: [{ product_id: sourceProduct.id, qty: 1 }],
            payments: [],
        }, cookies);
        expect(exchange.data.success).toBe(true);
        expect(exchange.data.data.new_sale_id).toBeDefined();
        expect(Math.abs(exchange.data.data.difference)).toBeLessThan(0.005);

        const after = await apiCall(`/returns/index.php?id=${returnId}`, 'GET', null, cookies);
        expect(after.data.data.return.status).toBe('refunded');
        expect(after.data.data.return.exchange_sale_id).toBe(exchange.data.data.new_sale_id);

        const newSale = await apiCall(`/sales/index.php?id=${exchange.data.data.new_sale_id}`, 'GET', null, cookies);
        expect(Number(newSale.data.data.sale.exchange_return_id)).toEqual(Number(returnId));
        expect(Math.abs(newSale.data.data.sale.grand_total - after.data.data.return.refund_total)).toBeLessThan(0.005);
    });

    test('linked exchange sale appears in sale detail', async () => {
        const detail = await apiCall(`/sales/index.php?id=${saleId}`, 'GET', null, cookies);
        expect(detail.data.data.returnable).toBeDefined();
        expect(detail.data.data.exchange).toBeDefined();
        expect(detail.data.data.exchange.allowed).toBe(false); // fully returned + exchanged
        expect(typeof detail.data.data.exchange.active).toBe('boolean');
    });

    test('a second exchange on the same return is rejected', async () => {
        const again = await apiCall('/returns/exchange.php', 'POST', {
            return_id: returnId,
            items: [],
            replacement_items: [{ product_id: sourceProduct.id, qty: 1 }],
            payments: [],
        }, cookies);
        expect(again.data.success).toBe(false);
    });
});