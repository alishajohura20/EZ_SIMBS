-- ============================================================
-- EZ_SIMBS - Phase 15: POS Exchanges
-- Run after ez_simbs_purchase_returns.sql (Phase 14).
-- Single enum widening; no new tables. Schema stays at 40.
-- ============================================================

-- An exchange settles the return by credit (no money leaves); the
-- negative-difference cash-out still records as 'original' so the
-- till ledger stays honest about real money.
ALTER TABLE sale_return_payments
    MODIFY method ENUM('original','cash','card','mobile','customer_balance','exchange')
        NOT NULL DEFAULT 'original';