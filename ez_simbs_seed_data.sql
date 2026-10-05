-- ============================================================================
--  EZ_SIMBS - SEED DATA FOR MANUAL FEATURE TESTING
--  Core pattern: 20 deterministic rows per table (ids 1001-1020). Four Phase-13
--  tables (sections 34-37) cover every return status but only what is coherent:
--    sale_returns        20 rows (ids 1001-1020)
--    sale_return_items   22 rows (ids 1001-1022; 1021/1022 are the extra lines
--                                of return 1011, which restocks 2 of 3 splits)
--    sale_return_payments 12 rows (ids 1001-1012; only REFUNDED returns pay)
--    return_photos       20 rows (ids 1001-1020)
--  The three Phase-14 tables (sections 38-40) follow the same contract, with
--  two deliberate count deviations: purchase_return_payments has 15 rows
--  (only CREDITED returns pay) and stock_logs gains 18 rows OUTSIDE the
--  verification window (1024-1041 - the supplier-return outbound rows plus the
--  one void-restore row), so check 1 keeps reporting stock_logs = 20 here:
--    purchase_returns       20 rows (ids 1001-1020; 5 of the 6 statuses -
--                           requested/rejected/received/credited/cancelled,
--                           leaving 'approved' to the live approve-
--                           walkthrough on 1013)
--    purchase_return_items  20 rows (ids 1001-1020, one line per return)
--    purchase_return_payments 15 rows (ids 1001-1015, credited returns only)
--  purchase_items and suppliers extend their existing seed with the Phase-14
--  columns: purchase_items.returned_qty (the receive() tally) and
--  suppliers.balance (the to_balance net of every credited return).
--  stock_logs carries 3 extra rows (1021-1023) for the return restocks, so it
--  is 23 rows; sale_items carries 2 extra ids (1021/1022) for the split of
--  sale 1009, so it is 22 rows. Everywhere else the 1001-1020 contract holds.
--
--  Mode     : ADDITIVE (nothing existing is deleted or modified)
--  Id range : 1001-1020 (kept far above existing ids to avoid clashes)
--  Password : admin123  (the bcrypt hash used is the same one the base
--             ez_simbs.sql seed uses for every demo account)
--
--  Run      : mysql -u aj -p ez_simbs < ez_simbs_seed_data.sql
--  Re-run   : not idempotent (plain INSERTs) - a second run fails on the
--             UNIQUE keys. See the "REMOVE" footer to re-seed cleanly.
--
--  See SEED_DATA.txt for credentials, id map and per-feature test scenarios.
-- ============================================================================

USE ez_simbs;

-- ---------------------------------------------------------------------------
-- 01. stores  (tenants - multi-store scoping, plan + status testing)
-- ---------------------------------------------------------------------------
INSERT INTO stores (id, name, owner_name, email, phone, address, currency, currency_symbol, plan, status, created_at, updated_at) VALUES
(1001,'Sunrise Mega Mart','Arif Hossain','owner.sunrise@seed.test','+8801711000001','House 12, Road 5, Banani, Dhaka-1213','BDT','BDT','pro','active','2026-03-05 09:15:00','2026-03-05 09:15:00'),
(1002,'Blue Ocean Superstore','Nusrat Jahan','owner.blueocean@seed.test','+8801711000002','Plot 88, Agrabad C/A, Chittagong-4000','BDT','BDT','pro','active','2026-03-08 10:20:00','2026-03-08 10:20:00'),
(1003,'Green Leaf Grocery','Rakib Hasan','owner.greenleaf@seed.test','+8801711000003','House 7, Dhanmondi Road 27, Dhaka-1209','BDT','BDT','free','active','2026-03-11 11:05:00','2026-03-11 11:05:00'),
(1004,'Star Electronics','Tanvir Ahmed','owner.starelec@seed.test','+8801711000004','Shop 210, Bashundhara City, Panthapath, Dhaka-1222','BDT','BDT','pro','active','2026-03-14 12:40:00','2026-03-14 12:40:00'),
(1005,'City Fresh Market','Sadia Islam','owner.cityfresh@seed.test','+8801711000005','Kachukhet Mor, Mirpur DOHS, Dhaka-1216','BDT','BDT','free','active','2026-03-17 09:50:00','2026-03-17 09:50:00'),
(1006,'Apex Hardware','Mahmudul Hasan','owner.apex@seed.test','+8801711000006','Naya Paltan, Islampur, Dhaka-1100','BDT','BDT','pro','pending','2026-03-20 14:10:00','2026-03-20 14:10:00'),
(1007,'Silver Fashion House','Farhana Akter','owner.silver@seed.test','+8801711000007','Bashundhara City Level 3, Dhaka-1222','BDT','BDT','free','active','2026-03-23 15:30:00','2026-03-23 15:30:00'),
(1008,'Metro Pharmacy','Kamrul Islam','owner.metro@seed.test','+8801711000008','Kachfiruz, Shyamoli, Dhaka-1207','BDT','BDT','pro','active','2026-03-26 08:45:00','2026-03-26 08:45:00'),
(1009,'Sunrise Outlet Gulshan','Arif Hossain','owner.sunrise2@seed.test','+8801711000009','Gulshan 2, Dhaka-1212','BDT','BDT','free','active','2026-04-02 10:00:00','2026-04-02 10:00:00'),
(1010,'Sunrise Outlet Uttara','Arif Hossain','owner.sunrise3@seed.test','+8801711000010','Sector 7, Uttara, Dhaka-1230','BDT','BDT','free','suspended','2026-04-05 11:20:00','2026-04-05 11:20:00'),
(1011,'Blue Ocean Outlet Sylhet','Nusrat Jahan','owner.blueocean2@seed.test','+8801711000011','Zindabazar, Sylhet-3100','BDT','BDT','free','active','2026-04-09 13:35:00','2026-04-09 13:35:00'),
(1012,'Green Leaf Outlet Mirpur','Rakib Hasan','owner.greenleaf2@seed.test','+8801711000012','Kazipara, Mirpur, Dhaka-1216','BDT','BDT','free','pending','2026-04-12 09:05:00','2026-04-12 09:05:00'),
(1013,'Star Electronics Lalmatia','Tanvir Ahmed','owner.starelec2@seed.test','+8801711000013','Lalmatia, Dhaka-1207','BDT','BDT','free','active','2026-04-16 16:15:00','2026-04-16 16:15:00'),
(1014,'City Fresh Express','Sadia Islam','owner.cityfresh2@seed.test','+8801711000014','Mohammadpur, Dhaka-1207','BDT','BDT','free','active','2026-04-20 10:40:00','2026-04-20 10:40:00'),
(1015,'Apex Hardware Bogura','Mahmudul Hasan','owner.apex2@seed.test','+8801711000015','Sherpur Road, Bogura-5400','BDT','BDT','free','active','2026-04-24 12:00:00','2026-04-24 12:00:00'),
(1016,'Silver Fashion Khulna','Farhana Akter','owner.silver2@seed.test','+8801711000016','Boyra More, Khulna-9100','BDT','BDT','free','suspended','2026-04-28 15:50:00','2026-04-28 15:50:00'),
(1017,'Metro Pharmacy Tongi','Kamrul Islam','owner.metro2@seed.test','+8801711000017','Tongi, Gazipur-1710','BDT','BDT','free','active','2026-05-03 09:30:00','2026-05-03 09:30:00'),
(1018,'Sunrise Warehouse','Arif Hossain','owner.sunrisewh@seed.test','+8801711000018','Jashore Sadar, Jashore-7400','BDT','BDT','pro','active','2026-05-07 14:25:00','2026-05-07 14:25:00'),
(1019,'Test Suspended Store','Test Owner','owner.suspended@seed.test','+8801711000019','Test Address Line 1, Test City','USD','$','free','suspended','2026-05-12 11:11:00','2026-05-12 11:11:00'),
(1020,'Test Pending Store','Test Owner','owner.pending@seed.test','+8801711000020','Test Address Line 2, Test City 2','USD','$','free','pending','2026-05-16 11:22:00','2026-05-16 11:22:00');

-- ---------------------------------------------------------------------------
-- 02. roles  (20 custom roles on top of the 5 built-in ones)
-- ---------------------------------------------------------------------------
INSERT INTO roles (id, name, description, created_at) VALUES
(1001,'store_owner_admin','Store owner with full control of one store','2026-03-05 09:20:00'),
(1002,'store_supervisor','Oversees daily store operations and staff','2026-03-05 09:20:00'),
(1003,'inventory_auditor','Read-only access to stock counts and audit logs','2026-03-05 09:20:00'),
(1004,'warehouse_operator','Handles goods receipt, transfer and damage entry','2026-03-05 09:20:00'),
(1005,'purchase_officer','Raises purchase orders, cannot receive stock','2026-03-05 09:20:00'),
(1006,'procurement_lead','Approves purchase orders and negotiates suppliers','2026-03-05 09:20:00'),
(1007,'sales_associate','Counter sales assistant who assists the cashier','2026-03-05 09:20:00'),
(1008,'senior_cashier','Full POS access plus end-of-day reconciliation','2026-03-05 09:20:00'),
(1009,'junior_cashier','Limited POS access, no refunds or cancellations','2026-03-05 09:20:00'),
(1010,'accountant','Invoicing, payments, ledgers and financial reports','2026-03-05 09:20:00'),
(1011,'hr_manager','Staff records, roles and shift planning','2026-03-05 09:20:00'),
(1012,'support_agent','Handles customer support tickets and returns','2026-03-05 09:20:00'),
(1013,'delivery_agent','Manages delivery orders and dispatch','2026-03-05 09:20:00'),
(1014,'marketing_officer','Banners, promotions, coupons and campaigns','2026-03-05 09:20:00'),
(1015,'stock_controller','Sets reorder levels, min stock, approves transfers','2026-03-05 09:20:00'),
(1016,'quality_inspector','Approves or rejects received goods','2026-03-05 09:20:00'),
(1017,'logistics_manager','Inter-branch and inter-store logistics','2026-03-05 09:20:00'),
(1018,'data_analyst','Read-only access to dashboards and reports','2026-03-05 09:20:00'),
(1019,'product_specialist','Maintains catalog, categories and brands','2026-03-05 09:20:00'),
(1020,'customer_support_lead','Leads customer support and the loyalty programme','2026-03-05 09:20:00');

-- ---------------------------------------------------------------------------
-- 03. permissions  (20 rows mapped onto the custom roles)
-- ---------------------------------------------------------------------------
INSERT INTO permissions (id, role_id, resource, action) VALUES
(1001,1001,'users','read'),
(1002,1001,'products','update'),
(1003,1002,'inventory','read'),
(1004,1003,'activity_logs','read'),
(1005,1004,'inventory','update'),
(1006,1005,'purchases','create'),
(1007,1006,'purchases','approve'),
(1008,1007,'sales','create'),
(1009,1008,'sales','read'),
(1010,1009,'sales','create'),
(1011,1010,'invoices','read'),
(1012,1011,'users','update'),
(1013,1012,'customers','read'),
(1014,1013,'orders','update'),
(1015,1014,'banners','manage'),
(1016,1014,'coupons','manage'),
(1017,1015,'products','update'),
(1018,1016,'purchases','approve'),
(1019,1017,'inventory','transfer'),
(1020,1018,'reports','read');

-- ---------------------------------------------------------------------------
-- 04. branches
--  1001-1004 belong to store 1 (EZ SIMBS Demo Store) so admin@ezsimbs.local
--  can see them in Multi-Branch views and the inventory branch selector.
--
--  manager_id is inserted as NULL and backfilled in section 05b, because
--  branches.manager_id -> users.id and users.branch_id -> branches.id form a
--  circular foreign key. Stores must exist first, so this stays here.
-- ---------------------------------------------------------------------------
INSERT INTO branches (id, store_id, name, address, phone, manager_id, status, created_at) VALUES
(1001,1,'Banani Outlet','House 12, Road 5, Banani, Dhaka-1213','+8801711000101',NULL,'active','2026-06-01 09:00:00'),
(1002,1,'Uttara Outlet','Sector 7, Uttara, Dhaka-1230','+8801711000102',NULL,'active','2026-06-01 09:05:00'),
(1003,1,'Warehouse (Main)','Dhanmondi Road 27, Dhaka-1209','+8801711000103',NULL,'active','2026-06-01 09:10:00'),
(1004,1,'Old Closed Outlet','Mirpur DOHS, Dhaka-1216','+8801711000104',NULL,'inactive','2026-06-01 09:15:00'),
(1005,1001,'Sunrise Main','House 12, Road 5, Banani, Dhaka-1213','+8801711000105',NULL,'active','2026-06-02 09:00:00'),
(1006,1001,'Sunrise Gulshan','Gulshan 2, Dhaka-1212','+8801711000106',NULL,'active','2026-06-02 09:05:00'),
(1007,1001,'Sunrise Uttara','Sector 7, Uttara, Dhaka-1230','+8801711000107',NULL,'active','2026-06-02 09:10:00'),
(1008,1002,'Blue Ocean Main','Plot 88, Agrabad C/A, Chittagong-4000','+8801711000108',NULL,'active','2026-06-03 09:00:00'),
(1009,1002,'Blue Ocean Agrabad','CDA Avenue, Chittagong-4000','+8801711000109',NULL,'active','2026-06-03 09:05:00'),
(1010,1003,'Green Leaf Main','House 7, Dhanmondi Road 27, Dhaka-1209','+8801711000110',NULL,'active','2026-06-04 09:00:00'),
(1011,1003,'Green Leaf Dhanmondi','Road 27, Dhaka-1209','+8801711000111',NULL,'active','2026-06-04 09:05:00'),
(1012,1004,'Star Electronics Main','Shop 210, Bashundhara City, Dhaka-1222','+8801711000112',NULL,'active','2026-06-05 09:00:00'),
(1013,1004,'Star Electronics Banani','Banani, Dhaka-1213','+8801711000113',NULL,'active','2026-06-05 09:05:00'),
(1014,1005,'City Fresh Main','Kachukhet Mor, Mirpur DOHS, Dhaka-1216','+8801711000114',NULL,'active','2026-06-06 09:00:00'),
(1015,1006,'Apex Hardware Main','Naya Paltan, Islampur, Dhaka-1100','+8801711000115',NULL,'active','2026-06-07 09:00:00'),
(1016,1007,'Silver Fashion Main','Bashundhara City Level 3, Dhaka-1222','+8801711000116',NULL,'active','2026-06-08 09:00:00'),
(1017,1008,'Metro Pharmacy Main','Kachfiruz, Shyamoli, Dhaka-1207','+8801711000117',NULL,'active','2026-06-09 09:00:00'),
(1018,1001,'Sunrise Warehouse','Jashore Sadar, Jashore-7400','+8801711000118',NULL,'active','2026-06-10 09:00:00'),
(1019,1002,'Blue Ocean Sylhet (Closed)','Zindabazar, Sylhet-3100','+8801711000119',NULL,'inactive','2026-06-11 09:00:00'),
(1020,1001,'Sunrise Test Branch','Test Road, Test Area, Dhaka-1000','+8801711000120',NULL,'inactive','2026-06-12 09:00:00');

-- ---------------------------------------------------------------------------
-- 05. users  (20 rows)
--  1001-1005 = the 5 role-gate logins used for sign-in testing.
--  1018 = inactive, 1019 = locked, 1020 = expired lockout (recovery case).
-- ---------------------------------------------------------------------------
INSERT INTO users (id, name, email, password, role_id, store_id, branch_id, phone, avatar, status, last_login, failed_attempts, lockout_until, created_at, updated_at) VALUES
(1001,'Seed Admin','seed.admin@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1,1,1,'+8801700000001',NULL,'active','2026-09-27 10:02:00',0,NULL,'2026-06-15 09:00:00','2026-09-27 10:02:00'),
(1002,'Seed Manager','seed.manager@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',2,1,2,'+8801700000002',NULL,'active','2026-09-27 09:41:00',0,NULL,'2026-06-15 09:00:00','2026-09-27 09:41:00'),
(1003,'Seed Branch Manager','seed.branch@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',3,1,3,'+8801700000003',NULL,'active','2026-09-26 18:12:00',0,NULL,'2026-06-15 09:00:00','2026-09-26 18:12:00'),
(1004,'Seed Cashier','seed.cashier@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1,1,'+8801700000004',NULL,'active','2026-09-27 11:30:00',0,NULL,'2026-06-15 09:00:00','2026-09-27 11:30:00'),
(1005,'Seed Customer','seed.customer@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',5,1,NULL,'+8801700000005',NULL,'active','2026-09-25 20:05:00',0,NULL,'2026-06-15 09:00:00','2026-09-25 20:05:00'),
(1006,'Arif Hossain','arif@sunrise.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1001,1001,1005,'+8801712000001',NULL,'active','2026-09-24 08:55:00',0,NULL,'2026-06-16 09:00:00','2026-09-24 08:55:00'),
(1007,'Nusrat Jahan','nusrat@blueocean.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',2,1002,1008,'+8801712000002',NULL,'active','2026-09-23 17:20:00',0,NULL,'2026-06-16 09:00:00','2026-09-23 17:20:00'),
(1008,'Rakib Hasan','rakib@greenleaf.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1002,1003,1010,'+8801712000003',NULL,'active','2026-09-22 12:00:00',0,NULL,'2026-06-16 09:00:00','2026-09-22 12:00:00'),
(1009,'Tanvir Ahmed','tanvir@starelec.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',3,1004,1012,'+8801712000004',NULL,'active','2026-09-21 15:45:00',0,NULL,'2026-06-17 09:00:00','2026-09-21 15:45:00'),
(1010,'Sadia Islam','sadia@cityfresh.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1005,1014,'+8801712000005',NULL,'active','2026-09-20 19:30:00',0,NULL,'2026-06-17 09:00:00','2026-09-20 19:30:00'),
(1011,'Mahmudul Hasan','mahmudul@apex.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1003,1006,1015,'+8801712000006',NULL,'active','2026-09-19 10:10:00',0,NULL,'2026-06-17 09:00:00','2026-09-19 10:10:00'),
(1012,'Farhana Akter','farhana@silver.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1005,1007,1016,'+8801712000007',NULL,'active','2026-09-18 14:00:00',0,NULL,'2026-06-18 09:00:00','2026-09-18 14:00:00'),
(1013,'Kamrul Islam','kamrul@metro.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1008,1017,'+8801712000008',NULL,'active','2026-09-17 16:25:00',0,NULL,'2026-06-18 09:00:00','2026-09-17 16:25:00'),
(1014,'Rehana Parvin','rehana@sunrise.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1008,1001,1006,'+8801712000009',NULL,'active','2026-09-16 13:35:00',0,NULL,'2026-06-18 09:00:00','2026-09-16 13:35:00'),
(1015,'Imran Kabir','imran@sunrise.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1004,1001,1018,'+8801712000010',NULL,'active','2026-09-15 07:50:00',0,NULL,'2026-06-19 09:00:00','2026-09-15 07:50:00'),
(1016,'Sumaiya Akter','sumaiya@sunrise.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',5,1001,NULL,'+8801712000011',NULL,'active','2026-09-14 21:15:00',0,NULL,'2026-06-19 09:00:00','2026-09-14 21:15:00'),
(1017,'Mehedi Hasan','mehedi@seed.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1009,1001,1007,'+8801700000017',NULL,'active','2026-09-13 11:05:00',0,NULL,'2026-06-19 09:00:00','2026-09-13 11:05:00'),
(1018,'Inactive Staff','inactive.user@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',2,1,2,'+8801700000018',NULL,'inactive','2026-07-30 10:00:00',0,NULL,'2026-06-20 09:00:00','2026-08-01 09:00:00'),
(1019,'Locked User','locked.user@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1,1,'+8801700000019',NULL,'locked','2026-09-10 08:00:00',5,'2026-09-28 12:00:00','2026-06-20 09:00:00','2026-09-10 08:00:00'),
(1020,'Recovery User','recovery.user@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1,1,'+8801700000020',NULL,'active','2026-09-05 09:30:00',3,'2026-09-05 09:45:00','2026-06-20 09:00:00','2026-09-05 09:45:00');

-- ---------------------------------------------------------------------------
-- 05b. branch manager backfill
--  Now that users exist, point each branch at its manager. Branches 1004,
--  1019 and 1020 stay unassigned (inactive/test branches).
-- ---------------------------------------------------------------------------
UPDATE branches SET manager_id = 9  WHERE id = 1001;
UPDATE branches SET manager_id = 9  WHERE id = 1002;
UPDATE branches SET manager_id = 8  WHERE id = 1003;
UPDATE branches SET manager_id = 1006 WHERE id IN (1005,1006,1007);
UPDATE branches SET manager_id = 1007 WHERE id IN (1008,1009);
UPDATE branches SET manager_id = 1008 WHERE id IN (1010,1011);
UPDATE branches SET manager_id = 1009 WHERE id IN (1012,1013);
UPDATE branches SET manager_id = 1010 WHERE id = 1014;
UPDATE branches SET manager_id = 1011 WHERE id = 1015;
UPDATE branches SET manager_id = 1012 WHERE id = 1016;
UPDATE branches SET manager_id = 1013 WHERE id = 1017;
UPDATE branches SET manager_id = 1015 WHERE id = 1018;

-- ---------------------------------------------------------------------------
-- 06. user_sessions  (20 rows - live, expired and remember-me sessions)
-- ---------------------------------------------------------------------------
INSERT INTO user_sessions (id, user_id, token, remember_token, ip_address, user_agent, device, created_at, last_activity, expires_at) VALUES
(1001,1001,'seed1a0f3c9d2b4e5f6a7b8c9d0e1f2a3b4c','rseed1001aaaaaaaaaaaaaaaaaaaaaaaa','103.10.12.4','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0 Safari/537.36','Windows - Chrome','2026-09-27 10:02:00','2026-09-27 18:40:00','2026-10-04 10:02:00'),
(1002,1001,'seed2b1f4d0e3c5f6a7b8c9d0e1f2a3b4c5d','rseed1002bbbbbbbbbbbbbbbbbbbbbbbb','103.10.12.4','Mozilla/5.0 (iPhone; CPU iPhone OS 17_4) Safari/604.1','iPhone - Safari','2026-09-26 08:15:00','2026-09-26 08:52:00','2026-09-27 08:15:00'),
(1003,1002,'seed3c2a5e1f4d6a7b8c9d0e1f2a3b4c5d6e','rseed1003cccccccccccccccccccccccc','103.10.12.7','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Firefox/126.0','macOS - Firefox','2026-09-27 09:41:00','2026-09-27 17:05:00','2026-10-04 09:41:00'),
(1004,1002,'seed4d3b6f2a5e7b8c9d0e1f2a3b4c5d6e7f','rseed1004dddddddddddddddddddddddd','49.12.44.9','Mozilla/5.0 (Linux; Android 14) Chrome Mobile 124.0','Android - Chrome','2026-09-20 12:00:00','2026-09-24 10:00:00','2026-09-21 12:00:00'),
(1005,1003,'seed5e4c7a3b6f8c9d0e1f2a3b4c5d6e7f8a','rseed1005eeeeeeeeeeeeeeeeeeeeeeee','103.10.12.11','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Edge/124.0','Windows - Edge','2026-09-26 18:12:00','2026-09-26 21:30:00','2026-10-03 18:12:00'),
(1006,1003,'seed6f5d8b4c7a9d0e1f2a3b4c5d6e7f8a9b','rseed1006ffffffffffffffffffffffff','103.10.12.11','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) Chrome/124.0','Linux - Chrome','2026-08-30 14:20:00','2026-08-30 15:00:00','2026-08-31 14:20:00'),
(1007,1004,'seed7a6e9c5d8b0e1f2a3b4c5d6e7f8a9b0c','rseed7000000000000000000000000000000','103.10.12.15','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-27 11:30:00','2026-09-27 19:12:00','2026-10-04 11:30:00'),
(1008,1004,'seed8b7f0d6e9c1f2a3b4c5d6e7f8a9b0c1d','rseed8111111111111111111111111111111','103.10.12.16','Mozilla/5.0 (iPad; CPU OS 17_4) Safari/604.1','iPad - Safari','2026-09-22 09:45:00','2026-09-26 12:00:00','2026-10-22 09:45:00'),
(1009,1005,'seed9c8a1e7f0d2a3b4c5d6e7f8a9b0c1d2e','rseed9222222222222222222222222222222','59.42.10.3','Mozilla/5.0 (iPhone; CPU iPhone OS 17_4) Chrome Mobile 124.0','iPhone - Chrome','2026-09-25 20:05:00','2026-09-27 09:00:00','2026-10-25 20:05:00'),
(1010,1005,'seed0d9b2f8a1e3b4c5d6e7f8a9b0c1d2e3f','rseed9333333333333333333333333333333','59.42.10.3','Mozilla/5.0 (iPhone; CPU iPhone OS 17_4) Chrome Mobile 124.0','iPhone - Chrome','2026-08-14 16:00:00','2026-08-14 16:30:00','2026-08-15 16:00:00'),
(1011,1006,'seed1e0c3a9b2f4c5d6e7f8a9b0c1d2e3f4a','rseed1044444444444444444444444444444','114.31.55.20','Mozilla/5.0 (Windows NT 11.0; Win64; x64) Chrome/125.0','Windows - Chrome','2026-09-24 08:55:00','2026-09-24 13:00:00','2026-10-24 08:55:00'),
(1012,1007,'seed2f1d4b0c3a5d6e7f8a9b0c1d2e3f4a5b','rseed1155555555555555555555555555555','114.31.55.21','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-23 17:20:00','2026-09-23 18:00:00','2026-10-23 17:20:00'),
(1013,1008,'seed3a2e5c1d4b6e7f8a9b0c1d2e3f4a5b6c','rseed1266666666666666666666666666666','114.31.55.22','Mozilla/5.0 (Linux; Android 14) Firefox/126.0','Android - Firefox','2026-09-22 12:00:00','2026-09-22 12:40:00','2026-10-22 12:00:00'),
(1014,1009,'seed4b3f6d2e5c7f8a9b0c1d2e3f4a5b6c7d','rseed1377777777777777777777777777777','118.25.99.6','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-21 15:45:00','2026-09-21 16:30:00','2026-10-21 15:45:00'),
(1015,1010,'seed5c4a7e3f6d8a9b0c1d2e3f4a5b6c7d8e','rseed1488888888888888888888888888888','118.25.99.7','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-20 19:30:00','2026-09-27 15:00:00','2026-10-20 19:30:00'),
(1016,1011,'seed6d5b8f4a7e9b0c1d2e3f4a5b6c7d8e9f','rseed1599999999999999999999999999999','101.11.3.9','Mozilla/5.0 (Linux; Ubuntu 24.04) Chrome/125.0','Linux - Chrome','2026-09-19 10:10:00','2026-09-19 11:00:00','2026-10-19 10:10:00'),
(1017,1012,'seed7e6c9a5b8f0c1d2e3f4a5b6c7d8e9f0a','rseed16aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','101.11.3.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-18 14:00:00','2026-09-18 14:45:00','2026-10-18 14:00:00'),
(1018,1013,'seed8f7d0b6c9a1d2e3f4a5b6c7d8e9f0a1b','rseed17bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb','101.11.3.11','Mozilla/5.0 (Android 14) Chrome Mobile 124.0','Android - Chrome','2026-09-17 16:25:00','2026-09-17 16:55:00','2026-10-17 16:25:00'),
(1019,1014,'seed9a8e1c7d0b2e3f4a5b6c7d8e9f0a1b2c','rseed18cccccccccccccccccccccccccccc','162.45.8.14','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-16 13:35:00','2026-09-27 09:20:00','2026-10-16 13:35:00'),
(1020,1017,'seed0b9f2d8e1c3f4a5b6c7d8e9f0a1b2c3d','rseed19dddddddddddddddddddddddddddddddd','162.45.8.15','Mozilla/5.0 (X11; Linux x86_64) Firefox/125.0','Linux - Firefox','2026-09-13 11:05:00','2026-09-13 11:40:00','2026-10-13 11:05:00');

-- ---------------------------------------------------------------------------
-- 07. categories  (20 rows - parent/child hierarchy + inactive entries)
-- ---------------------------------------------------------------------------
INSERT INTO categories (id, name, parent_id, status, created_at) VALUES
(1001,'Beverages',NULL,'active','2026-06-25 09:00:00'),
(1002,'Soft Drinks',1001,'active','2026-06-25 09:01:00'),
(1003,'Energy Drinks',1001,'active','2026-06-25 09:02:00'),
(1004,'Milk & Juice',1001,'active','2026-06-25 09:03:00'),
(1005,'Packaged Foods',NULL,'active','2026-06-25 09:04:00'),
(1006,'Snacks & Chips',1005,'active','2026-06-25 09:05:00'),
(1007,'Noodles',1005,'active','2026-06-25 09:06:00'),
(1008,'Dairy & Frozen',NULL,'active','2026-06-25 09:07:00'),
(1009,'Milk Powder',1008,'active','2026-06-25 09:08:00'),
(1010,'Frozen Treats',1008,'active','2026-06-25 09:09:00'),
(1011,'Personal Care',NULL,'active','2026-06-25 09:10:00'),
(1012,'Hair Care',1011,'active','2026-06-25 09:11:00'),
(1013,'Oral Care',1011,'active','2026-06-25 09:12:00'),
(1014,'Body Care',1011,'active','2026-06-25 09:13:00'),
(1015,'Household',NULL,'active','2026-06-25 09:14:00'),
(1016,'Cleaning Supplies',1015,'active','2026-06-25 09:15:00'),
(1017,'Electronics',NULL,'active','2026-06-25 09:16:00'),
(1018,'Mobile Accessories',1017,'active','2026-06-25 09:17:00'),
(1019,'Stationery',NULL,'active','2026-06-25 09:18:00'),
(1020,'Discontinued Test Category',NULL,'inactive','2026-06-25 09:19:00');

-- ---------------------------------------------------------------------------
-- 08. brands  (20 rows - 19 active + 1 inactive)
-- ---------------------------------------------------------------------------
INSERT INTO brands (id, name, logo, status, created_at) VALUES
(1001,'Nestle',NULL,'active','2026-06-25 10:00:00'),
(1002,'Coca-Cola',NULL,'active','2026-06-25 10:01:00'),
(1003,'PepsiCo',NULL,'active','2026-06-25 10:02:00'),
(1004,'PRAN',NULL,'active','2026-06-25 10:03:00'),
(1005,'Square',NULL,'active','2026-06-25 10:04:00'),
(1006,'Rownd',NULL,'active','2026-06-25 10:05:00'),
(1007,'Aarong',NULL,'active','2026-06-25 10:06:00'),
(1008,'Marico',NULL,'active','2026-06-25 10:07:00'),
(1009,'Unilever',NULL,'active','2026-06-25 10:08:00'),
(1010,'Pepsodent',NULL,'active','2026-06-25 10:09:00'),
(1011,'Savlon',NULL,'active','2026-06-25 10:10:00'),
(1012,'Dettol',NULL,'active','2026-06-25 10:11:00'),
(1013,'Rin',NULL,'active','2026-06-25 10:12:00'),
(1014,'Lux',NULL,'active','2026-06-25 10:13:00'),
(1015,'Gillette',NULL,'active','2026-06-25 10:14:00'),
(1016,'Vivo',NULL,'active','2026-06-25 10:15:00'),
(1017,'Realme',NULL,'active','2026-06-25 10:16:00'),
(1018,'Samsung',NULL,'active','2026-06-25 10:17:00'),
(1019,'Red Bull',NULL,'active','2026-06-25 10:18:00'),
(1020,'Generic Brand (Inactive)',NULL,'inactive','2026-06-25 10:19:00');

-- ---------------------------------------------------------------------------
-- 09. units  (20 rows)
-- ---------------------------------------------------------------------------
INSERT INTO units (id, name, symbol, created_at) VALUES
(1001,'Bottle','btl','2026-06-25 11:00:00'),
(1002,'Carton','ctn','2026-06-25 11:01:00'),
(1003,'Pack','pk','2026-06-25 11:02:00'),
(1004,'Bag','bag','2026-06-25 11:03:00'),
(1005,'Bottle (Small)','btl-s','2026-06-25 11:04:00'),
(1006,'Tube','tube','2026-06-25 11:05:00'),
(1007,'Pack of 6','pk6','2026-06-25 11:06:00'),
(1008,'Set','set','2026-06-25 11:07:00'),
(1009,'Piece (Small)','pcs','2026-06-25 11:08:00'),
(1010,'Can','can','2026-06-25 11:09:00'),
(1011,'Dozen','dz','2026-06-25 11:10:00'),
(1012,'Roll','roll','2026-06-25 11:11:00'),
(1013,'Sack (50kg)','sack50','2026-06-25 11:12:00'),
(1014,'Box of 24','box24','2026-06-25 11:13:00'),
(1015,'Gallon','gal','2026-06-25 11:14:00'),
(1016,'Tray','tray','2026-06-25 11:15:00'),
(1017,'Litre Bottle','lb','2026-06-25 11:16:00'),
(1018,'Strip','strip','2026-06-25 11:17:00'),
(1019,'Tablet Pack','tab','2026-06-25 11:18:00'),
(1020,'Sack','sack','2026-06-25 11:19:00');

-- ---------------------------------------------------------------------------
-- 10. products  (20 rows)
--  total_stock / branch_count mirror the seeded `inventory` rows exactly.
--  Statuses cover active / inactive / discontinued.
-- ---------------------------------------------------------------------------
INSERT INTO products (id, sku, name, category_id, brand_id, unit_id, image, barcode, description, price, cost, min_stock, reorder_level, total_stock, branch_count, status, created_at, updated_at) VALUES
(1001,'SEED-SKU-1001','Coca-Cola Soft Drink 750ml',1002,1002,1001,'assets/images/products/seed-01.svg','8801000001001','Chilled sparkling soft drink, 750ml PET bottle.',65.00,48.00,10,25,155,2,'active','2026-06-26 09:00:00','2026-09-20 09:00:00'),
(1002,'SEED-SKU-1002','Pepsi Max Soft Drink 750ml',1002,1003,1001,'assets/images/products/seed-02.svg','8801000001002','Zero-sugar cola, 750ml PET bottle.',62.00,45.00,10,25,80,2,'active','2026-06-26 09:01:00','2026-09-20 09:01:00'),
(1003,'SEED-SKU-1003','Red Bull Energy Drink 250ml',1003,1019,1010,'assets/images/products/seed-03.svg','8801000001003','Sugar-free energy drink, 250ml slim can.',220.00,185.00,8,20,108,2,'active','2026-06-26 09:02:00','2026-09-19 09:02:00'),
(1004,'SEED-SKU-1004','Nestle Milo Powder 400g',1004,1001,1002,'assets/images/products/seed-04.svg','8801000001004','Chocolate malt drink powder, 400g tin.',480.00,415.00,12,30,84,2,'active','2026-06-26 09:03:00','2026-09-18 09:03:00'),
(1005,'SEED-SKU-1005','PRAN Mango Juice 1L',1004,1004,1001,'assets/images/products/seed-05.svg','8801000001005','Mango fruit juice, 1L PET bottle.',95.00,72.00,15,35,108,1,'active','2026-06-26 09:04:00','2026-09-17 09:04:00'),
(1006,'SEED-SKU-1006','Square Potato Chips 40g',1006,1005,1003,'assets/images/products/seed-06.svg','8801000001006','Salted potato chips, 40g pack.',25.00,18.00,20,50,95,1,'active','2026-06-26 09:05:00','2026-09-16 09:05:00'),
(1007,'SEED-SKU-1007','Rownd Chanachur 100g',1006,1006,1003,'assets/images/products/seed-07.svg','8801000001007','Spicy mixed snack, 100g pack.',20.00,15.00,25,60,40,1,'active','2026-06-26 09:06:00','2026-09-15 09:06:00'),
(1008,'SEED-SKU-1008','Aarong Full Cream Milk Powder 900g',1009,1007,1002,'assets/images/products/seed-08.svg','8801000001008','Instant full cream milk powder, 900g tin.',890.00,760.00,6,12,72,1,'active','2026-06-26 09:07:00','2026-09-14 09:07:00'),
(1009,'SEED-SKU-1009','Marico Shine Hair Oil 150ml',1012,1008,1005,'assets/images/products/seed-09.svg','8801000001009','Non-greasy hair oil, 150ml bottle.',165.00,128.00,12,25,15,1,'active','2026-06-26 09:08:00','2026-09-13 09:08:00'),
(1010,'SEED-SKU-1010','Pepsodent Toothpaste 200g',1013,1010,1006,'assets/images/products/seed-10.svg','8801000001010','Cavity protection toothpaste, 200g tube.',185.00,140.00,10,20,62,1,'active','2026-06-26 09:09:00','2026-09-12 09:09:00'),
(1011,'SEED-SKU-1011','Lux Beauty Soap 100g',1014,1014,1007,'assets/images/products/seed-11.svg','8801000001011','Moisturising beauty soap, 100g x 6 pack.',48.00,36.00,30,80,5,1,'active','2026-06-26 09:10:00','2026-09-11 09:10:00'),
(1012,'SEED-SKU-1012','Dettol Antibacterial Soap 100g',1014,1012,1007,'assets/images/products/seed-12.svg','8801000001012','Antibacterial protection soap, 100g x 6 pack.',72.00,55.00,30,80,5,1,'active','2026-06-26 09:11:00','2026-09-11 09:11:00'),
(1013,'SEED-SKU-1013','Savlon Hand Wash 250ml',1014,1011,1005,'assets/images/products/seed-13.svg','8801000001013','Antibacterial hand wash, 250ml bottle.',210.00,165.00,12,25,30,1,'active','2026-06-26 09:12:00','2026-09-10 09:12:00'),
(1014,'SEED-SKU-1014','Vivo Y21 Smartphone 128GB',1018,1016,1008,'assets/images/products/seed-14.svg','8801000001014','5G smartphone, 128GB internal storage.',18500.00,17200.00,3,6,0,1,'active','2026-06-26 09:13:00','2026-09-09 09:13:00'),
(1015,'SEED-SKU-1015','Realme Buds Wireless Earbuds',1018,1017,1008,'assets/images/products/seed-15.svg','8801000001015','True wireless earbuds with charging case.',2450.00,2150.00,4,8,0,1,'active','2026-06-26 09:14:00','2026-09-09 09:14:00'),
(1016,'SEED-SKU-1016','Samsung 64GB USB Flash Drive',1018,1018,1009,'assets/images/products/seed-16.svg','8801000001016','USB 3.1 flash drive, 64GB.',1250.00,1080.00,6,12,55,1,'active','2026-06-26 09:15:00','2026-09-08 09:15:00'),
(1017,'SEED-SKU-1017','A4 Notebook 200 Pages',1019,NULL,1004,'assets/images/products/seed-17.svg','8801000001017','A4 ruled notebook, 200 pages. No brand assigned.',185.00,130.00,20,40,0,0,'active','2026-06-26 09:16:00','2026-09-08 09:16:00'),
(1018,'SEED-SKU-1018','Ballpoint Pen Box of 12',1019,NULL,1007,'assets/images/products/seed-18.svg','8801000001018','Blue ballpoint pens, box of 12. No brand assigned.',240.00,175.00,15,30,0,0,'active','2026-06-26 09:17:00','2026-09-08 09:17:00'),
(1019,'SEED-SKU-1019','Discontinued Wireless Mouse',1018,1011,1008,'assets/images/products/seed-19.svg','8801000001019','Legacy wireless mouse - discontinued line, hidden from lists.',650.00,480.00,0,0,0,0,'discontinued','2026-06-26 09:18:00','2026-07-30 09:18:00'),
(1020,'SEED-SKU-1020','Seasonal Gift Hamper',1019,1014,1008,'assets/images/products/seed-20.svg','8801000001020','Seasonal hamper, temporarily deactivated for stock take.',1450.00,1100.00,5,10,0,0,'inactive','2026-06-26 09:19:00','2026-07-30 09:19:00');

-- ---------------------------------------------------------------------------
-- 11. suppliers  (20 rows - ratings 0.00 to 4.95, 2 inactive)
--  balance reflects the Phase-14 credits that overflowed the PO's due
--  (suppliers.balance = net to_balance, one asset the store can draw on
--  through Purchase::applySupplierBalance / api/suppliers/balance.php).
-- ---------------------------------------------------------------------------
INSERT INTO suppliers (id, company_name, contact_person, email, phone, address, tax_id, rating, status, balance, created_at, updated_at) VALUES
(1001,'Sunrise Foods & Beverages Ltd','Arif Hossain','sales@sunrisefoods.test','+8801712001001','Level 4, Bashundhara City, Dhaka-1222','BIN-1001-2233-44',4.80,'active',6760.00,'2026-06-26 10:00:00','2026-09-20 10:00:00'),
(1002,'Blue Ocean Trading Co.','Nusrat Jahan','info@blueocean.test','+8801712001002','Plot 88, Agrabad C/A, Chittagong-4000','BIN-1002-5566-77',4.50,'active',396.00,'2026-06-26 10:01:00','2026-09-18 10:01:00'),
(1003,'Green Leaf Distributors','Rakib Hasan','contact@greenleaf.test','+8801712001003','House 7, Dhanmondi Road 27, Dhaka-1209','BIN-1003-8899-00',3.90,'active',0.00,'2026-06-26 10:02:00','2026-09-16 10:02:00'),
(1004,'Bengal FMCG Distributors','Tanvir Ahmed','sales@bengalfmcg.test','+8801712001004','Naya Paltan, Islampur, Dhaka-1100','BIN-1004-1122-33',4.20,'active',475.20,'2026-06-26 10:03:00','2026-09-15 10:03:00'),
(1005,'Star Electronics Supply','Sadia Islam','procurement@starelec.test','+8801712001005','Shop 210, Bashundhara City, Dhaka-1222','BIN-1005-4455-66',4.70,'active',18920.00,'2026-06-26 10:04:00','2026-09-14 10:04:00'),
(1006,'Apex Hardware & Tools','Mahmudul Hasan','apex@hardwares.test','+8801712001006','Islampur Road, Dhaka-1100','BIN-1006-7788-99',3.50,'active',0.00,'2026-06-26 10:05:00','2026-09-12 10:05:00'),
(1007,'Silver Fashion Wholesale','Farhana Akter','wholesale@silverfashion.test','+8801712001007','Bashundhara City Level 3, Dhaka-1222','BIN-1007-0011-22',4.00,'active',1650.00,'2026-06-26 10:06:00','2026-09-10 10:06:00'),
(1008,'Metro Pharma Supply','Kamrul Islam','orders@metropharma.test','+8801712001008','Shyamoli, Dhaka-1207','BIN-1008-3344-55',4.60,'active',844.80,'2026-06-26 10:07:00','2026-09-09 10:07:00'),
(1009,'Delta Paper Products','Rehana Parvin','hello@deltapaper.test','+8801712001009','Gulshan 1, Dhaka-1212','BIN-1009-6677-88',3.20,'active',0.00,'2026-06-26 10:08:00','2026-09-08 10:08:00'),
(1010,'Canton Electronics Import','Imran Kabir','import@cantonelec.test','+8801712001010','Jashore Sadar, Jashore-7400','BIN-1010-9900-11',4.10,'active',11847.00,'2026-06-26 10:09:00','2026-09-07 10:09:00'),
(1011,'Meghna Consumer Products','Sumaiya Akter','sales@meghna.test','+8801712001011','Zindabazar, Sylhet-3100','BIN-1011-2233-44',3.80,'active',3344.00,'2026-06-26 10:10:00','2026-09-06 10:10:00'),
(1012,'Jamuna Rice & Cereals','Mehedi Hasan','order@jamunarice.test','+8801712001012','Sherpur Road, Bogura-5400','BIN-1012-4455-66',4.30,'active',0.00,'2026-06-26 10:11:00','2026-09-05 10:11:00'),
(1013,'Padma Confectionery','Arif Hossain','order@padmaconf.test','+8801712001013','Boyra More, Khulna-9100','BIN-1013-5566-77',2.90,'active',0.00,'2026-06-26 10:12:00','2026-09-04 10:12:00'),
(1014,'Dhaka Packaging Supplies','Seed Admin','sales@dhakapack.test','+8801712001014','Tongi, Gazipur-1710','BIN-1014-7788-99',4.05,'active',0.00,'2026-06-26 10:13:00','2026-09-03 10:13:00'),
(1015,'Karnaphuli Chemicals','Seed Manager','supply@karnaphuli.test','+8801712001015','Kachfiruz, Chittagong-4000','BIN-1015-8899-00',3.00,'active',0.00,'2026-06-26 10:14:00','2026-09-02 10:14:00'),
(1016,'Old Contract Supplier (Closed)','Old Contact','old@contract.test','+8801712001016','Old Address, Old City','BIN-1016-1010-11',1.50,'inactive',0.00,'2026-06-26 10:15:00','2026-07-15 10:15:00'),
(1017,'Sungroud Transport & Freight','Imran Kabir','logistics@sungroud.test','+8801712001017','Jashore Sadar, Jashore-7400','BIN-1017-2121-32',4.40,'active',0.00,'2026-06-26 10:16:00','2026-09-01 10:16:00'),
(1018,'Test Supplier - No Rating','Test Person','test@supplier.test','+8801712001018','Test Street 1, Test City','BIN-1018-3131-43',0.00,'active',0.00,'2026-06-26 10:17:00','2026-06-26 10:17:00'),
(1019,'Test Supplier - Suspended','Test Person','suspended@supplier.test','+8801712001019','Test Street 2, Test City','BIN-1019-4141-54',2.00,'inactive',0.00,'2026-06-26 10:18:00','2026-08-01 10:18:00'),
(1020,'Premium Import House','Tanvir Ahmed','premium@import.test','+8801712001020','Level 8, Bashundhara City, Dhaka-1222','BIN-1020-5151-65',4.95,'active',0.00,'2026-06-26 10:19:00','2026-08-28 10:19:00');

-- ---------------------------------------------------------------------------
-- 12. customers  (20 rows - VIP flags, loyalty points, running balances)
-- ---------------------------------------------------------------------------
INSERT INTO customers (id, name, phone, email, address, membership_id, loyalty_points, balance, is_vip, notes, status, created_at, updated_at) VALUES
(1001,'Rashedul Islam','+8801811000001','rashedul@customer.test','House 5, Road 7, Dhanmondi, Dhaka-1209','SEED-MEM-1001',2450,1250.00,1,'VIP regular since 2024. Prefers WhatsApp updates.','active','2026-06-26 12:00:00','2026-09-20 12:00:00'),
(1002,'Shahana Akter','+8801811000002','shahana@customer.test','Flat B3, Road 11, Banani, Dhaka-1213','SEED-MEM-1002',1830,0.00,1,'Wholesale buyer - small quantity discount applies.','active','2026-06-26 12:01:00','2026-09-19 12:01:00'),
(1003,'Mahbubur Rahman','+8801811000003','mahbubur@customer.test','House 22, Mirpur DOHS, Dhaka-1216','SEED-MEM-1003',960,340.50,0,'Regular walk-in customer.','active','2026-06-26 12:02:00','2026-09-18 12:02:00'),
(1004,'Nasrin Sultana','+8801811000004','nasrin@customer.test','Flat 4B, Shyamoli, Dhaka-1207','SEED-MEM-1004',720,0.00,0,NULL,'active','2026-06-26 12:03:00','2026-09-17 12:03:00'),
(1005,'Jahangir Alam','+8801811000005','jahangir@customer.test','House 9, Uttara Sector 4, Dhaka-1230','SEED-MEM-1005',1480,0.00,1,'Often buys in bulk on weekends.','active','2026-06-26 12:04:00','2026-09-16 12:04:00'),
(1006,'Rokeya Begum','+8801811000006','rokeya@customer.test','House 41, Kazipara, Mirpur, Dhaka-1216','SEED-MEM-1006',540,75.00,0,NULL,'active','2026-06-26 12:05:00','2026-09-15 12:05:00'),
(1007,'Sohel Rana','+8801811000007','sohel@customer.test','House 17, Agrabad, Chittagong-4000','SEED-MEM-1007',1120,0.00,0,NULL,'active','2026-06-26 12:06:00','2026-09-14 12:06:00'),
(1008,'Tanvir Ahmed (Walk-in)',NULL,NULL,NULL,'SEED-MEM-1008',60,0.00,0,'No phone / email on file - cash customer.','active','2026-06-26 12:07:00','2026-09-13 12:07:00'),
(1009,'Farhana Yasmin','+8801811000009','farhana@customer.test','Flat 7A, Lalmatia, Dhaka-1207','SEED-MEM-1009',890,2150.00,0,'Owes a running balance - settle at next visit.','active','2026-06-26 12:08:00','2026-09-12 12:08:00'),
(1010,'Kamrul Hasan','+8801811000010','kamrul@customer.test','House 30, Zindabazar, Sylhet-3100','SEED-MEM-1010',410,0.00,0,NULL,'active','2026-06-26 12:09:00','2026-09-11 12:09:00'),
(1011,'Lutfun Nahar','+8801811000011','lutfun@customer.test','House 3, Boyra More, Khulna-9100','SEED-MEM-1011',330,90.00,0,NULL,'active','2026-06-26 12:10:00','2026-09-10 12:10:00'),
(1012,'Shafiqur Rahman','+8801811000012','shafiqur@customer.test','House 14, Sherpur Road, Bogura-5400','SEED-MEM-1012',275,0.00,0,NULL,'active','2026-06-26 12:11:00','2026-09-09 12:11:00'),
(1013,'Morjina Khatun','+8801811000013','morjina@customer.test','House 55, Tongi Bazar, Gazipur-1710','SEED-MEM-1013',195,0.00,0,NULL,'active','2026-06-26 12:12:00','2026-09-08 12:12:00'),
(1014,'Alamgir Hossain','+8801811000014','alamgir@customer.test','House 8, Gulshan 2, Dhaka-1212','SEED-MEM-1014',1580,480.00,1,'Corporate buyer - monthly invoice.','active','2026-06-26 12:13:00','2026-09-07 12:13:00'),
(1015,'Rubina Parvin','+8801811000015','rubina@customer.test','Flat 2C, Kalabagan, Dhaka-1207','SEED-MEM-1015',150,0.00,0,NULL,'active','2026-06-26 12:14:00','2026-09-06 12:14:00'),
(1016,'Sadiqur Rahman','+8801811000016','sadiqur@customer.test','House 19, Cantonment, Dhaka-1202','SEED-MEM-1016',95,0.00,0,NULL,'active','2026-06-26 12:15:00','2026-09-05 12:15:00'),
(1017,'Nusrat Jahan (Customer)','+8801811000017','nusrat.c@customer.test','House 12, Bashundhara, Dhaka-1222','SEED-MEM-1017',70,0.00,0,NULL,'active','2026-06-26 12:16:00','2026-09-04 12:16:00'),
(1018,'Test Customer - Inactive','+8801811000018','inactive.cust@customer.test','Test Address 1','SEED-MEM-1018',0,0.00,0,'Dormant account - used to test the active/inactive filter.','inactive','2026-06-26 12:17:00','2026-07-20 12:17:00'),
(1019,'Test Customer - VIP No Notes','+8801811000019','vip.cust@customer.test','Test Address 2','SEED-MEM-1019',5000,0.00,1,NULL,'active','2026-06-26 12:18:00','2026-09-03 12:18:00'),
(1020,'Walk-in Cash Customer','+8801811000020',NULL,NULL,'SEED-MEM-1020',25,0.00,0,'Minimal record - phone only.','active','2026-06-26 12:19:00','2026-09-02 12:19:00');

-- ---------------------------------------------------------------------------
-- 13. purchases  (20 rows - all 5 statuses, all 4 payment_status values)
--  paid / due / payment_status reconcile with the purchase_payments rows.
-- ---------------------------------------------------------------------------
INSERT INTO purchases (id, supplier_id, po_number, status, subtotal, tax, total, paid, due, payment_status, notes, invoice_file, created_by, approved_by, branch_id, created_at, updated_at) VALUES
(1001,1001,'SEED-PO-20260601-0001','received',4800.00,480.00,5280.00,5280.00,0.00,'paid','Monthly restock for Main Branch beverage shelf.',NULL,1,8,1,'2026-06-01 10:00:00','2026-06-03 15:20:00'),
(1002,1002,'SEED-PO-20260604-0002','received',3600.00,360.00,3960.00,3960.00,0.00,'paid','Chips and snacks - paid in two part payments.',NULL,1,8,1,'2026-06-04 11:20:00','2026-06-07 12:00:00'),
(1003,1004,'SEED-PO-20260608-0003','approved',8640.00,864.00,9504.00,4752.00,4752.00,'partial','Bulk juice order awaiting goods receipt.',NULL,1002,8,1,'2026-06-08 09:35:00','2026-06-11 10:05:00'),
(1004,1001,'SEED-PO-20260612-0004','received',9250.00,0.00,9250.00,9250.00,0.00,'paid','Energy drink clearance - tax exempt (resale certificate on file).',NULL,1,8,1,'2026-06-12 14:10:00','2026-06-15 11:00:00'),
(1005,1003,'SEED-PO-20260616-0005','pending',27300.00,2730.00,30030.00,30030.00,0.00,'paid','Personal care bulk - submitted, awaiting approval.',NULL,1002,NULL,2,'2026-06-16 10:45:00','2026-06-18 09:30:00'),
(1006,1008,'SEED-PO-20260620-0006','received',7680.00,768.00,8448.00,8448.00,0.00,'paid','Hair oil and personal care range.',NULL,1,8,1,'2026-06-20 13:00:00','2026-06-23 16:40:00'),
(1007,1009,'SEED-PO-20260624-0007','pending',11200.00,1120.00,12320.00,2000.00,10320.00,'partial','Stationery restock - partially paid.',NULL,1002,NULL,1,'2026-06-24 09:15:00','2026-06-26 11:00:00'),
(1008,1007,'SEED-PO-20260628-0008','received',24650.00,2465.00,27115.00,27115.00,0.00,'paid','Soap and hand wash - quarterly order.',NULL,1,8,2,'2026-06-28 15:30:00','2026-07-02 10:20:00'),
(1009,1005,'SEED-PO-20260702-0009','approved',27000.00,2700.00,29700.00,29700.00,0.00,'paid','Electronics accessories - approved, goods not yet received.',NULL,1002,8,1,'2026-07-02 11:25:00','2026-07-05 09:50:00'),
(1010,1005,'SEED-PO-20260706-0010','received',103200.00,10320.00,113520.00,113520.00,0.00,'paid','Smartphone consignment order.',NULL,1,8,1,'2026-07-06 10:10:00','2026-07-10 14:00:00'),
(1011,1010,'SEED-PO-20260710-0011','received',86100.00,8610.00,94710.00,94710.00,0.00,'paid','Audio accessories - paid in three instalments.',NULL,1,8,1,'2026-07-10 09:00:00','2026-07-14 17:30:00'),
(1012,1004,'SEED-PO-20260714-0012','pending',16600.00,1660.00,18260.00,5000.00,13260.00,'partial','Milo powder top-up - partial payment received.',NULL,1002,NULL,1,'2026-07-14 11:45:00','2026-07-16 10:20:00'),
(1013,1011,'SEED-PO-20260718-0013','received',9120.00,912.00,10032.00,10032.00,0.00,'paid','Milk powder order paid by cheque.',NULL,1,8,3,'2026-07-18 09:30:00','2026-07-21 12:15:00'),
(1014,1012,'SEED-PO-20260722-0014','received',3700.00,370.00,4070.00,0.00,4070.00,'pending','Rice and cereals - small order. Goods received, invoice still unpaid.',NULL,1002,8,1,'2026-07-22 10:00:00','2026-07-24 11:00:00'),
(1015,1013,'SEED-PO-20260726-0015','received',2880.00,288.00,3168.00,3168.00,0.00,'paid','Confectionery restock.',NULL,1,8,1,'2026-07-26 13:40:00','2026-07-28 09:50:00'),
(1016,1016,'SEED-PO-20260730-0016','cancelled',0.00,0.00,0.00,0.00,0.00,'pending','Cancelled - supplier closed down before confirmation. No items.',NULL,1,NULL,1,'2026-07-30 10:00:00','2026-08-01 10:00:00'),
(1017,1014,'SEED-PO-20260803-0017','draft',2250.00,0.00,2250.00,0.00,2250.00,'pending','Draft for low stock snacks - still editable.',NULL,1,NULL,1,'2026-08-03 08:30:00','2026-08-03 08:30:00'),
(1018,1015,'SEED-PO-20260807-0018','draft',1800.00,0.00,1800.00,0.00,1800.00,'pending','Draft PO with one line - test the edit / add items flow.',NULL,1002,NULL,1,'2026-08-07 16:20:00','2026-08-07 16:20:00'),
(1019,1017,'SEED-PO-20260811-0019','draft',0.00,0.00,0.00,0.00,0.00,'pending','Empty draft - used to test the PO creation wizard from scratch.',NULL,1,NULL,1,'2026-08-11 09:15:00','2026-08-11 09:15:00'),
(1020,1018,'SEED-PO-20260815-0020','draft',0.00,0.00,0.00,0.00,0.00,'pending','Empty draft for the no-rating test supplier.',NULL,1002,NULL,2,'2026-08-15 11:00:00','2026-08-15 11:00:00');

-- ---------------------------------------------------------------------------
-- 14. purchase_items  (20 rows)
--  1016 (cancelled) and 1019/1020 (empty drafts) intentionally carry no items.
--  Every purchase with a non-zero subtotal has at least one matching line, and
--  SUM(purchase_items.total) equals purchases.subtotal (check #5b).
--  returned_qty is the Phase-14 receive() tally: the units shipped back and NOT
--  subsequently voided. It reconciles with sections 38-40:
--    1001 -> 100/100 (10 + 90, the ceiling return leaves nothing returnable)
--    1002 -> 22/200   1003 -> 66/120 (the split-credit return, see section 38)
--    1004 -> 8/50    1007 -> 21/60 (6 credited + 15 received; 1017 voided=0)
--    1009 -> 4/90   1010 -> 6/70
--    1012 -> 1/6    1013 -> 3/30   1014 -> 4/20
--    1016 -> 4/12   1017 -> 4/20
--  all others 0 (1005/1006/1008/1011/1015/1019/1020 have no fulfilled returns).
-- ---------------------------------------------------------------------------
INSERT INTO purchase_items (id, purchase_id, product_id, qty, received_qty, unit_cost, total, returned_qty) VALUES
(1001,1001,1001,100,100,48.00,4800.00,100),
(1002,1002,1006,200,200,18.00,3600.00,22),
(1003,1003,1005,120,0,72.00,8640.00,66),
(1004,1004,1003,50,50,185.00,9250.00,8),
(1005,1005,1011,300,0,36.00,10800.00,0),
(1006,1005,1012,300,0,55.00,16500.00,0),
(1007,1006,1009,60,60,128.00,7680.00,21),
(1008,1007,1010,80,0,140.00,11200.00,0),
(1009,1008,1013,90,90,165.00,14850.00,4),
(1010,1008,1010,70,70,140.00,9800.00,6),
(1011,1009,1016,25,0,1080.00,27000.00,0),
(1012,1010,1014,6,6,17200.00,103200.00,1),
(1013,1011,1015,30,30,2150.00,64500.00,3),
(1014,1011,1016,20,20,1080.00,21600.00,4),
(1015,1012,1004,40,0,415.00,16600.00,0),
(1016,1013,1008,12,12,760.00,9120.00,4),
(1017,1014,1003,20,20,185.00,3700.00,4),
(1018,1015,1001,60,60,48.00,2880.00,0),
(1019,1018,1006,100,0,18.00,1800.00,0),
(1020,1017,1007,150,0,15.00,2250.00,0);

-- ---------------------------------------------------------------------------
-- 15. purchase_payments  (20 rows - sums match `purchases.paid` exactly)
-- ---------------------------------------------------------------------------
INSERT INTO purchase_payments (id, purchase_id, amount, payment_method, reference, notes, paid_by, created_at) VALUES
(1001,1001,3000.00,'cash','CASH-0001','First instalment on delivery.',1,'2026-06-02 11:00:00'),
(1002,1001,2280.00,'bank_transfer','TRF-0001','Balance settled by transfer.',1,'2026-06-02 11:05:00'),
(1003,1002,2000.00,'cash','CASH-0002','Advance against delivery.',1,'2026-06-04 11:30:00'),
(1004,1002,1960.00,'card','CARD-0002','Second part payment.',1,'2026-06-06 10:15:00'),
(1005,1003,4752.00,'cash','CASH-0003','Half payment against approved PO.',1002,'2026-06-09 09:40:00'),
(1006,1004,9250.00,'bank_transfer','TRF-0004','Full payment, tax-exempt invoice.',1,'2026-06-13 10:20:00'),
(1007,1005,10000.00,'cash','CASH-0005','Token advance before approval.',1002,'2026-06-17 09:10:00'),
(1008,1005,20030.00,'bank_transfer','TRF-0005','Balance on approval.',1,'2026-06-18 09:35:00'),
(1009,1006,8448.00,'card','CARD-0006','Paid in full by card.',1,'2026-06-21 12:00:00'),
(1010,1007,2000.00,'cash','CASH-0007','Part payment on pending PO.',1002,'2026-06-25 10:30:00'),
(1011,1008,27115.00,'bank_transfer','TRF-0008','Full payment.',1,'2026-06-29 11:00:00'),
(1012,1009,15000.00,'cash','CASH-0009','Part payment on approved PO.',1002,'2026-07-03 10:00:00'),
(1013,1009,14700.00,'card','CARD-0009','Balance on card.',1002,'2026-07-04 15:20:00'),
(1014,1010,113520.00,'bank_transfer','TRF-0010','Full payment for consignment order.',1,'2026-07-07 09:00:00'),
(1015,1011,25000.00,'cash','CASH-0011','Instalment 1 of 3.',1,'2026-07-10 09:30:00'),
(1016,1011,25000.00,'bank_transfer','TRF-0011','Instalment 2 of 3.',1,'2026-07-11 14:00:00'),
(1017,1011,44710.00,'cash','CASH-0012','Instalment 3 of 3 - closes the balance.',1,'2026-07-14 17:25:00'),
(1018,1012,5000.00,'cash','CASH-0013','Part payment on pending PO.',1002,'2026-07-15 10:10:00'),
(1019,1013,10032.00,'cheque','CHQ-0014','Paid by crossed cheque #4417.',1,'2026-07-19 11:20:00'),
(1020,1015,3168.00,'bank_transfer','TRF-0016','Full payment for confectionery.',1,'2026-07-27 09:40:00');

-- ---------------------------------------------------------------------------
-- 16. sales  (20 rows - spread over 2026-07-31 .. 2026-09-27 so date-range
--  filters and dashboard charts have data; amounts match the sale_items below)
--  Statuses: 7 completed, 1 held, 1 cancelled, 7 returned, 4 partially_returned
--  (returned: 1001, 1007, 1009, 1010, 1017, 1020 + legacy 1004;
--   partially_returned: 1002, 1003, 1006, 1014 - driven by sections 34-37)
-- ---------------------------------------------------------------------------
INSERT INTO sales (id, customer_id, invoice_no, subtotal, discount, tax, shipping, grand_total, payment_method, status, notes, created_by, branch_id, created_at, updated_at) VALUES
(1001,1001,'SEED-INV-20260731-0001',195.00,0.00,19.50,0.00,214.50,'cash','returned','Counter sale - soft drinks.',10,1,'2026-07-31 10:12:00','2026-07-31 10:12:00'),
(1002,1002,'SEED-INV-20260803-0002',310.00,10.00,30.00,0.00,330.00,'card','partially_returned','Card sale with line discount.',10,1,'2026-08-03 15:40:00','2026-08-03 15:40:00'),
(1003,1003,'SEED-INV-20260806-0003',200.00,0.00,20.00,50.00,270.00,'mobile','partially_returned','Mobile payment plus delivery charge.',10,1,'2026-08-06 12:05:00','2026-08-06 12:05:00'),
(1004,1004,'SEED-INV-20260810-0004',180.00,0.00,18.00,0.00,198.00,'cash','returned','Fully returned - goods damaged on delivery.',10,1,'2026-08-10 18:22:00','2026-08-12 11:00:00'),
(1005,1005,'SEED-INV-20260814-0005',440.00,40.00,40.00,0.00,440.00,'card','completed','VIP customer discount applied.',10,1,'2026-08-14 14:30:00','2026-08-14 14:30:00'),
(1006,1006,'SEED-INV-20260818-0006',570.00,0.00,57.00,0.00,627.00,'cash','partially_returned','Juice pack sale.',10,1,'2026-08-18 09:55:00','2026-08-18 09:55:00'),
(1007,1009,'SEED-INV-20260822-0007',165.00,15.00,15.00,0.00,165.00,'mobile','returned','Running-balance customer purchase.',10,1,'2026-08-22 16:10:00','2026-08-22 16:10:00'),
(1008,1004,'SEED-INV-20260826-0008',370.00,0.00,37.00,60.00,467.00,'card','held','Held sale - customer left to fetch cash. Never resumed.',10,1,'2026-08-26 19:44:00','2026-08-26 19:44:00'),
(1009,1010,'SEED-INV-20260830-0009',576.00,76.00,50.00,0.00,550.00,'cash','returned','Promotional bundle discount.',10,1,'2026-08-30 11:20:00','2026-08-30 11:20:00'),
(1010,1011,'SEED-INV-20260903-0010',576.00,0.00,57.60,0.00,633.60,'mobile','returned','Mobile payment.',10,1,'2026-09-03 13:35:00','2026-09-03 13:35:00'),
(1011,1012,'SEED-INV-20260906-0011',210.00,10.00,20.00,0.00,220.00,'card','completed','Card sale.',10,1,'2026-09-06 10:05:00','2026-09-06 10:05:00'),
(1012,1014,'SEED-INV-20260909-0012',18000.00,0.00,1800.00,0.00,19800.00,'card','completed','Corporate smartphone order.',10,1,'2026-09-09 17:50:00','2026-09-09 17:50:00'),
(1013,1003,'SEED-INV-20260912-0013',2500.00,250.00,225.00,0.00,2475.00,'card','completed','Flash drive bulk order.',10,1,'2026-09-12 12:15:00','2026-09-12 12:15:00'),
(1014,1004,'SEED-INV-20260914-0014',960.00,0.00,96.00,0.00,1056.00,'cash','partially_returned','Milk powder purchase.',10,1,'2026-09-14 10:40:00','2026-09-14 10:40:00'),
(1015,1005,'SEED-INV-20260916-0015',850.00,0.00,85.00,0.00,935.00,'card','completed','Milk powder with line discount.',10,1,'2026-09-16 16:00:00','2026-09-16 16:00:00'),
(1016,1007,'SEED-INV-20260918-0016',4900.00,100.00,480.00,0.00,5280.00,'card','completed','Wireless earbuds - VIP customer.',10,1,'2026-09-18 14:20:00','2026-09-18 14:20:00'),
(1017,1005,'SEED-INV-20260920-0017',260.00,10.00,25.00,0.00,275.00,'mixed','returned','Mixed payment - see sale_payments 1016/1017.',10,1,'2026-09-20 09:50:00','2026-09-20 09:50:00'),
(1018,1006,'SEED-INV-20260922-0018',186.00,0.00,18.60,40.00,244.60,'mixed','completed','Mixed payment with delivery charge.',10,1,'2026-09-22 11:35:00','2026-09-22 11:35:00'),
(1019,1008,'SEED-INV-20260925-0019',350.00,0.00,35.00,0.00,385.00,'cash','cancelled','Cancelled at the counter - stock restored.',10,1,'2026-09-25 15:10:00','2026-09-25 15:25:00'),
(1020,1009,'SEED-INV-20260927-0020',630.00,30.00,60.00,55.00,715.00,'mobile','returned','Hand wash order with express shipping.',10,1,'2026-09-27 12:15:00','2026-09-27 12:15:00');

-- ---------------------------------------------------------------------------
-- 17. sale_items  (22 rows - ids 1001-1022. Every sale has one line except
--  sale 1009, whose 12-unit line is split into 3 lines of 4 (1009, 1021, 1022)
--  so the Phase-13 return 1011 can restock only part of it. Lines 1021/1022
--  sit outside the 1001-1020 verification window on purpose.
--  returned_qty is the cached counter the returns flow maintains: it equals
--  the units received back so far on that line (see sections 34-37).
--  Breakdown:
--    1001 -> 3/3 returned         1002 -> 1/5 returned
--    1003 -> 1/8 returned         1006 -> 3/6 returned
--    1007 -> 1/1 returned         1009,1021,1022 -> 4/4 each returned
--    1010 -> 8/8 returned          1014 -> 1/2 returned
--    1017 -> 4/4 returned         1020 -> 3/3 returned
--    all others 0 (incl. 1018: its return 1018 was VOIDED, tally reverted)
-- ---------------------------------------------------------------------------
INSERT INTO sale_items (id, sale_id, product_id, qty, unit_price, discount, total, returned_qty) VALUES
(1001,1001,1001,3,65.00,0.00,195.00,3),
(1002,1002,1002,5,62.00,0.00,310.00,1),
(1003,1003,1006,8,25.00,0.00,200.00,1),
(1004,1004,1007,10,20.00,20.00,180.00,0),
(1005,1005,1003,2,220.00,0.00,440.00,0),
(1006,1006,1005,6,95.00,0.00,570.00,3),
(1007,1007,1009,1,165.00,0.00,165.00,1),
(1008,1008,1010,2,185.00,0.00,370.00,0),
(1009,1009,1011,4,48.00,0.00,192.00,4),
(1021,1009,1011,4,48.00,0.00,192.00,4),
(1022,1009,1011,4,48.00,0.00,192.00,4),
(1010,1010,1012,8,72.00,0.00,576.00,8),
(1011,1011,1013,1,210.00,0.00,210.00,0),
(1012,1012,1014,1,18500.00,500.00,18000.00,0),
(1013,1013,1016,2,1250.00,0.00,2500.00,0),
(1014,1014,1004,2,480.00,0.00,960.00,1),
(1015,1015,1008,1,890.00,40.00,850.00,0),
(1016,1016,1015,2,2450.00,0.00,4900.00,0),
(1017,1017,1001,4,65.00,0.00,260.00,4),
(1018,1018,1002,3,62.00,0.00,186.00,0),
(1019,1019,1006,15,25.00,25.00,350.00,0),
(1020,1020,1013,3,210.00,0.00,630.00,3);

-- ---------------------------------------------------------------------------
-- 18. sale_payments  (20 rows - 2 mixed sales are split into 2 rows each;
--  the held sale has none, the cancelled sale keeps a single refund row)
-- ---------------------------------------------------------------------------
INSERT INTO sale_payments (id, sale_id, amount, payment_method, reference, notes, received_by, created_at) VALUES
(1001,1001,214.50,'cash',NULL,NULL,10,'2026-07-31 10:12:00'),
(1002,1002,330.00,'card','CARD-SEED-1002',NULL,10,'2026-08-03 15:40:00'),
(1003,1003,270.00,'mobile','MOB-SEED-1003',NULL,10,'2026-08-06 12:05:00'),
(1004,1005,440.00,'card','CARD-SEED-1005',NULL,10,'2026-08-14 14:30:00'),
(1005,1006,627.00,'cash',NULL,NULL,10,'2026-08-18 09:55:00'),
(1006,1007,165.00,'mobile','MOB-SEED-1007',NULL,10,'2026-08-22 16:10:00'),
(1007,1009,550.00,'cash',NULL,NULL,10,'2026-08-30 11:20:00'),
(1008,1010,633.60,'mobile','MOB-SEED-1010',NULL,10,'2026-09-03 13:35:00'),
(1009,1011,220.00,'card','CARD-SEED-1011',NULL,10,'2026-09-06 10:05:00'),
(1010,1012,19800.00,'card','CARD-SEED-1012','Corporate order',10,'2026-09-09 17:50:00'),
(1011,1013,2475.00,'card','CARD-SEED-1013',NULL,10,'2026-09-12 12:15:00'),
(1012,1014,1056.00,'cash',NULL,NULL,10,'2026-09-14 10:40:00'),
(1013,1015,935.00,'card','CARD-SEED-1015',NULL,10,'2026-09-16 16:00:00'),
(1014,1016,5280.00,'card','CARD-SEED-1016',NULL,10,'2026-09-18 14:20:00'),
(1015,1017,150.00,'cash',NULL,'Part 1 of mixed payment',10,'2026-09-20 09:50:00'),
(1016,1017,125.00,'card','CARD-SEED-1017','Part 2 of mixed payment',10,'2026-09-20 09:50:00'),
(1017,1018,104.60,'mobile','MOB-SEED-1018','Part 1 of mixed payment',10,'2026-09-22 11:35:00'),
(1018,1018,140.00,'card','CARD-SEED-1018','Part 2 of mixed payment',10,'2026-09-22 11:35:00'),
(1019,1019,385.00,'cash',NULL,'Refund issued for cancelled sale',10,'2026-09-25 15:25:00'),
(1020,1020,715.00,'mobile','MOB-SEED-1020',NULL,10,'2026-09-27 12:15:00');

-- ---------------------------------------------------------------------------
-- 19. inventory  (20 rows)
--  Rows 1001-1020 are allocated so that SUM(qty) per product equals
--  products.total_stock, and COUNT(rows) per product equals branch_count:
--    products 1001-1004 -> 2 branches each (rows 1001-1008)
--    products 1005-1016 -> 1 branch each  (rows 1009-1020)
--    products 1017-1020 -> no inventory row (branch_count = 0)
--  qty values are the CURRENT balance, i.e. after the seeded sales.
-- ---------------------------------------------------------------------------
INSERT INTO inventory (id, product_id, branch_id, qty, min_stock, reorder_level, updated_at) VALUES
(1001,1001,1,95,10,25,'2026-09-27 09:00:00'),
(1002,1001,2,60,10,20,'2026-09-27 09:00:00'),
(1003,1002,1,45,10,25,'2026-09-27 09:00:00'),
(1004,1002,2,35,10,20,'2026-09-27 09:00:00'),
(1005,1003,1,60,8,20,'2026-09-27 09:00:00'),
(1006,1003,2,48,8,15,'2026-09-27 09:00:00'),
(1007,1004,1,50,12,30,'2026-09-27 09:00:00'),
(1008,1004,2,34,12,25,'2026-09-27 09:00:00'),
(1009,1005,1,108,15,35,'2026-09-27 09:00:00'),
(1010,1006,1,95,20,50,'2026-09-27 09:00:00'),
(1011,1007,1,40,25,60,'2026-09-27 09:00:00'),
(1012,1008,1,72,6,12,'2026-09-27 09:00:00'),
(1013,1009,1,15,12,25,'2026-09-27 09:00:00'),
(1014,1010,1,62,10,20,'2026-09-27 09:00:00'),
(1015,1011,1,5,30,80,'2026-09-27 09:00:00'),
(1016,1012,1,5,30,80,'2026-09-27 09:00:00'),
(1017,1013,1,30,12,25,'2026-09-27 09:00:00'),
(1018,1014,1,0,3,6,'2026-09-27 09:00:00'),
(1019,1015,1,0,4,8,'2026-09-27 09:00:00'),
(1020,1016,1,55,6,12,'2026-09-27 09:00:00');

-- ---------------------------------------------------------------------------
-- 20. stock_logs  (23 rows - 20 base + 3 Phase-13 restock rows 1021-1023)
--  NOTE: with only 20 base rows these logs are a SAMPLE of movements, not a full
--  ledger that reconciles to the inventory balances above. Do not expect
--  SUM(in) - SUM(out) to equal inventory.qty.
--  Rows 1021-1023 are the 'sale_return' restocks for returns 1011 (2 of its
--  3 lines back into sellable stock) and 1012 (1 unit). They reference the
--  RETURN id (reference_id = sale_return id) exactly as Inventory::recordReturn
--  writes them at receive() time. The 1010 restock is the pre-existing row 1018.
-- ---------------------------------------------------------------------------
INSERT INTO stock_logs (id, product_id, branch_id, type, qty, reference_id, reference_type, notes, user_id, created_at) VALUES
(1001,1001,1,'in',100,1001,'purchase','Goods received against SEED-PO-20260601-0001.',1,'2026-06-03 15:20:00'),
(1002,1006,1,'in',200,1002,'purchase','Chips received - second part payment cleared.',1,'2026-06-07 12:00:00'),
(1003,1003,1,'in',50,1004,'purchase','Energy drinks received - tax-exempt invoice.',1,'2026-06-15 11:00:00'),
(1004,1008,1,'in',12,1013,'purchase','Milk powder received at warehouse branch.',8,'2026-07-21 12:15:00'),
(1005,1010,1,'in',70,1008,'purchase','Soap consignment received - quarterly order.',1,'2026-07-02 10:20:00'),
(1006,1014,1,'in',6,1010,'purchase','Smartphone consignment stock received.',1,'2026-07-10 14:00:00'),
(1007,1016,1,'in',20,1011,'purchase','Flash drives received - paid in 3 instalments.',1,'2026-07-14 17:30:00'),
(1008,1001,1,'out',3,1001,'sale','POS sale SEED-INV-20260731-0001.',10,'2026-07-31 10:12:00'),
(1009,1002,1,'out',5,1002,'sale','POS sale SEED-INV-20260803-0002.',10,'2026-08-03 15:40:00'),
(1010,1006,1,'out',8,1003,'sale','POS sale SEED-INV-20260806-0003.',10,'2026-08-06 12:05:00'),
(1011,1007,1,'out',10,1004,'sale','POS sale - later returned, stock restored.',10,'2026-08-10 18:22:00'),
(1012,1011,1,'out',12,1009,'sale','POS sale SEED-INV-20260830-0009. Sale 1008 is on hold and correctly produced NO stock movement.',10,'2026-08-30 10:05:00'),
(1013,1016,1,'out',2,1013,'sale','Flash drive bulk order.',10,'2026-09-12 12:15:00'),
(1014,1004,1,'out',2,1014,'sale','Milk powder purchase.',10,'2026-09-14 10:40:00'),
(1015,1015,1,'out',2,1016,'sale','Earbuds sold to VIP customer.',10,'2026-09-18 14:20:00'),
(1016,1003,1,'adjustment',-2,NULL,'stock_take','Physical count correction - 2 cans missing.',9,'2026-09-20 17:00:00'),
(1017,1009,1,'damage',-1,NULL,'damage_write_off','Bottle leaked in storage - written off.',9,'2026-09-21 11:30:00'),
(1018,1012,1,'return',8,1010,'sale_return','Dettol returned from SEED-INV-20260903-0010 (8 of the 8 units sold).',10,'2026-09-05 11:00:00'),
(1019,1002,2,'transfer',20,NULL,'transfer','Transfer from branch 1 to branch 2 to clear stock (product 1002 is stocked at both branches).',1,'2026-08-05 14:20:00'),
(1020,1011,1,'out',1,NULL,'manual_issue','Staff consumption - sample write-off.',1,'2026-09-23 10:45:00'),
(1021,1011,1,'in',4,1011,'sale_return','Restock from SEED-RET-1011 line A - crate resellable.',10,'2026-09-10 10:30:00'),
(1022,1011,1,'in',4,1011,'sale_return','Restock from SEED-RET-1011 line B - crate resellable.',10,'2026-09-10 10:30:00'),
(1023,1006,1,'in',1,1012,'sale_return','Restock from SEED-RET-1012 - single pack resellable.',10,'2026-08-09 11:00:00');

-- ---------------------------------------------------------------------------
-- 21. invoices  (20 rows - one per sale, qr_code holds the JSON payload that
--  classes/Invoice.php generates: invoice no, grand total and sale timestamp)
-- ---------------------------------------------------------------------------
INSERT INTO invoices (id, sale_id, pdf_path, qr_code, created_at) VALUES
(1001,1001,'invoices/SEED-INV-20260731-0001.pdf','{"invoice":"SEED-INV-20260731-0001","total":214.50,"date":"2026-07-31 10:12:00"}','2026-07-31 10:12:05'),
(1002,1002,'invoices/SEED-INV-20260803-0002.pdf','{"invoice":"SEED-INV-20260803-0002","total":330.00,"date":"2026-08-03 15:40:00"}','2026-08-03 15:40:05'),
(1003,1003,'invoices/SEED-INV-20260806-0003.pdf','{"invoice":"SEED-INV-20260806-0003","total":270.00,"date":"2026-08-06 12:05:00"}','2026-08-06 12:05:05'),
(1004,1004,'invoices/SEED-INV-20260810-0004.pdf','{"invoice":"SEED-INV-20260810-0004","total":198.00,"date":"2026-08-10 18:22:00"}','2026-08-10 18:22:05'),
(1005,1005,'invoices/SEED-INV-20260814-0005.pdf','{"invoice":"SEED-INV-20260814-0005","total":440.00,"date":"2026-08-14 14:30:00"}','2026-08-14 14:30:05'),
(1006,1006,'invoices/SEED-INV-20260818-0006.pdf','{"invoice":"SEED-INV-20260818-0006","total":627.00,"date":"2026-08-18 09:55:00"}','2026-08-18 09:55:05'),
(1007,1007,'invoices/SEED-INV-20260822-0007.pdf','{"invoice":"SEED-INV-20260822-0007","total":165.00,"date":"2026-08-22 16:10:00"}','2026-08-22 16:10:05'),
(1008,1008,NULL,'{"invoice":"SEED-INV-20260826-0008","total":467.00,"date":"2026-08-26 19:44:00"}','2026-08-26 19:44:05'),
(1009,1009,'invoices/SEED-INV-20260830-0009.pdf','{"invoice":"SEED-INV-20260830-0009","total":550.00,"date":"2026-08-30 11:20:00"}','2026-08-30 11:20:05'),
(1010,1010,'invoices/SEED-INV-20260903-0010.pdf','{"invoice":"SEED-INV-20260903-0010","total":633.60,"date":"2026-09-03 13:35:00"}','2026-09-03 13:35:05'),
(1011,1011,'invoices/SEED-INV-20260906-0011.pdf','{"invoice":"SEED-INV-20260906-0011","total":220.00,"date":"2026-09-06 10:05:00"}','2026-09-06 10:05:05'),
(1012,1012,'invoices/SEED-INV-20260909-0012.pdf','{"invoice":"SEED-INV-20260909-0012","total":19800.00,"date":"2026-09-09 17:50:00"}','2026-09-09 17:50:05'),
(1013,1013,'invoices/SEED-INV-20260912-0013.pdf','{"invoice":"SEED-INV-20260912-0013","total":2475.00,"date":"2026-09-12 12:15:00"}','2026-09-12 12:15:05'),
(1014,1014,'invoices/SEED-INV-20260914-0014.pdf','{"invoice":"SEED-INV-20260914-0014","total":1056.00,"date":"2026-09-14 10:40:00"}','2026-09-14 10:40:05'),
(1015,1015,'invoices/SEED-INV-20260916-0015.pdf','{"invoice":"SEED-INV-20260916-0015","total":935.00,"date":"2026-09-16 16:00:00"}','2026-09-16 16:00:05'),
(1016,1016,'invoices/SEED-INV-20260918-0016.pdf','{"invoice":"SEED-INV-20260918-0016","total":5280.00,"date":"2026-09-18 14:20:00"}','2026-09-18 14:20:05'),
(1017,1017,'invoices/SEED-INV-20260920-0017.pdf','{"invoice":"SEED-INV-20260920-0017","total":275.00,"date":"2026-09-20 09:50:00"}','2026-09-20 09:50:05'),
(1018,1018,'invoices/SEED-INV-20260922-0018.pdf','{"invoice":"SEED-INV-20260922-0018","total":244.60,"date":"2026-09-22 11:35:00"}','2026-09-22 11:35:05'),
(1019,1019,NULL,'{"invoice":"SEED-INV-20260925-0019","total":385.00,"date":"2026-09-25 15:10:00"}','2026-09-25 15:10:05'),
(1020,1020,'invoices/SEED-INV-20260927-0020.pdf','{"invoice":"SEED-INV-20260927-0020","total":715.00,"date":"2026-09-27 12:15:00"}','2026-09-27 12:15:05');

-- ---------------------------------------------------------------------------
-- 22. notifications  (20 rows - all 4 types, read + unread, relative links)
-- ---------------------------------------------------------------------------
INSERT INTO notifications (id, user_id, title, message, type, is_read, link, created_at) VALUES
(1001,1001,'Low stock: Lux Beauty Soap','Product SEED-SKU-1011 has 5 pcs left, reorder level is 80.','warning',0,'inventory.php','2026-09-27 08:05:00'),
(1002,1001,'Low stock: Dettol Antibacterial Soap','Product SEED-SKU-1012 has 5 pcs left, reorder level is 80.','warning',0,'inventory.php','2026-09-27 08:06:00'),
(1003,1001,'Out of stock: Vivo Y21 Smartphone','SEED-SKU-1014 is at 0 qty across all branches.','danger',0,'inventory.php','2026-09-27 08:07:00'),
(1004,1002,'Purchase order approved','SEED-PO-20260706-0010 approved for store 1.','success',1,'purchases.php','2026-07-05 09:50:00'),
(1005,1002,'Purchase order cancelled','SEED-PO-20260730-0016 was cancelled - supplier closed.','danger',1,'purchases.php','2026-08-01 10:00:00'),
(1006,1002,'Supplier rating updated','Premium Import House is now rated 4.95.','info',0,'suppliers.php','2026-08-28 10:20:00'),
(1007,1003,'Branch transfer completed','20 units of SEED-SKU-1005 moved to branch 1005.','success',1,'inventory.php','2026-08-05 14:25:00'),
(1008,1003,'Stock take discrepancy','Physical count found 2 missing SEED-SKU-1003 units.','warning',0,'inventory.php','2026-09-20 17:05:00'),
(1009,1004,'Daily Z-report ready','Counter day summary is available for 2026-09-27.','info',0,'reports.php','2026-09-27 20:00:00'),
(1010,1004,'Held sale needs attention','Sale SEED-INV-20260826-0008 is still on hold.','warning',0,'sales.php','2026-08-26 19:45:00'),
(1011,1005,'Order shipped','Your order SEED-ORD-20260910-1012 has been shipped.','success',0,'orders.php','2026-09-11 15:30:00'),
(1012,1005,'Return requested','Your return request for SEED-ORD-20260916-1014 is under review.','info',0,'orders.php','2026-09-17 10:00:00'),
(1013,1005,'New coupon available','Use WELCOME20 for 20% off your next order.','success',0,'store.php','2026-09-01 08:00:00'),
(1014,1006,'Purchase order submitted','SEED-PO-20260616-0005 submitted for approval.','info',1,'purchases.php','2026-06-16 10:45:00'),
(1015,1007,'Payment received','Full payment received against SEED-PO-20260604-0002.','success',1,'purchases.php','2026-06-07 12:00:00'),
(1016,1008,'Payment received','Full payment received against SEED-PO-20260714-0012.','success',1,'purchases.php','2026-07-16 10:20:00'),
(1017,1009,'Product activated','SEED-SKU-1019 is now active and visible.','info',0,'products.php','2026-07-30 09:20:00'),
(1018,1010,'New order received','Storefront order SEED-ORD-20260920-1016 needs confirmation.','warning',0,'orders.php','2026-09-20 11:00:00'),
(1019,1011,'Review approved','Thanks - your review is now live on the product page.','success',1,'store.php','2026-09-12 12:00:00'),
(1020,1012,'Loyalty points expiring','You have points expiring soon. Redeem before month end.','warning',0,'dashboard.php','2026-09-25 08:00:00');

-- ---------------------------------------------------------------------------
-- 23. activity_logs  (20 rows - audit trail for the seeded activity)
-- ---------------------------------------------------------------------------
INSERT INTO activity_logs (id, user_id, action, resource, resource_id, old_value, new_value, ip_address, browser, created_at) VALUES
(1001,1001,'user.login','users',1001,NULL,'role=admin store=1 branch=1','103.10.12.4','Chrome 124.0 / Windows','2026-09-27 10:02:00'),
(1002,1002,'user.login','users',1002,NULL,'role=manager store=1 branch=2','103.10.12.7','Firefox 126.0 / macOS','2026-09-27 09:41:00'),
(1003,1003,'user.login','users',1003,NULL,'role=branch_manager store=1 branch=3','103.10.12.11','Edge 124.0 / Windows','2026-09-26 18:12:00'),
(1004,1004,'user.login','users',1004,NULL,'role=cashier store=1 branch=1','103.10.12.15','Chrome 124.0 / Windows','2026-09-27 11:30:00'),
(1005,1005,'user.login','users',1005,NULL,'role=customer store=1','59.42.10.3','Chrome Mobile 124.0 / iPhone','2026-09-25 20:05:00'),
(1006,10,'sale.create','sales',1020,'NULL','total=715.00 status=completed method=mobile','103.10.12.15','Chrome 124.0 / Windows','2026-09-27 12:15:00'),
(1007,10,'sale.create','sales',1017,'NULL','total=275.00 status=completed method=mixed','103.10.12.15','Chrome 124.0 / Windows','2026-09-20 09:50:00'),
(1008,10,'sale.hold','sales',1008,'status=completed','status=held','103.10.12.15','Chrome 124.0 / Windows','2026-08-26 19:44:00'),
(1009,10,'sale.cancel','sales',1019,'status=completed','status=cancelled','103.10.12.15','Chrome 124.0 / Windows','2026-09-25 15:25:00'),
(1010,10,'sale.return','sales',1004,'status=completed','status=returned','103.10.12.15','Chrome 124.0 / Windows','2026-08-12 11:00:00'),
(1011,1,'purchase.approve','purchases',1003,'status=pending','status=approved','103.10.12.4','Chrome 124.0 / Windows','2026-06-11 10:05:00'),
(1012,1,'purchase.receive','purchases',1004,'status=approved','status=received','103.10.12.4','Chrome 124.0 / Windows','2026-06-15 11:00:00'),
(1013,8,'purchase.create','purchases',1017,'NULL','status=draft total=2250.00','103.10.12.7','Firefox 126.0 / macOS','2026-08-03 08:30:00'),
(1014,1,'purchase.cancel','purchases',1016,'status=pending','status=cancelled','103.10.12.4','Chrome 124.0 / Windows','2026-08-01 10:00:00'),
(1015,9,'stock.adjustment','products',1003,'qty=62','qty=60','103.10.12.11','Edge 124.0 / Windows','2026-09-20 17:00:00'),
(1016,9,'stock.damage','products',1009,'qty=16','qty=15','103.10.12.11','Edge 124.0 / Windows','2026-09-21 11:30:00'),
(1017,1,'stock.transfer','inventory',1004,'branch=1 qty=45','branch=2 qty=65','103.10.12.4','Chrome 124.0 / Windows','2026-08-05 14:20:00'),
(1018,1,'product.update','products',1020,'status=active','status=inactive','103.10.12.4','Chrome 124.0 / Windows','2026-07-30 09:19:00'),
(1019,1,'order.create','orders',1016,'NULL','total=5280.00 status=delivered','59.42.10.3','Chrome Mobile 124.0 / iPhone','2026-09-20 11:00:00'),
(1020,1,'review.create','product_reviews',1012,'NULL','rating=5 status=approved','59.42.10.3','Chrome Mobile 124.0 / iPhone','2026-09-12 12:00:00');

-- ---------------------------------------------------------------------------
-- 24. settings  (20 rows - keys prefixed SEED_ so they never clash with the
--  13 live company_*/tax_rate/... settings already in the database)
-- ---------------------------------------------------------------------------
INSERT INTO settings (id, setting_key, setting_value, setting_group, created_at, updated_at) VALUES
(1001,'SEED_storefront_enabled','1','general','2026-06-30 09:00:00','2026-06-30 09:00:00'),
(1002,'SEED_storefront_currency','BDT','general','2026-06-30 09:01:00','2026-06-30 09:01:00'),
(1003,'SEED_default_tax_rate','10','general','2026-06-30 09:02:00','2026-06-30 09:02:00'),
(1004,'SEED_low_stock_alert_email','inventory@ezsimbs.test','notifications','2026-06-30 09:03:00','2026-06-30 09:03:00'),
(1005,'SEED_login_max_attempts','5','security','2026-06-30 09:04:00','2026-06-30 09:04:00'),
(1006,'SEED_login_lockout_minutes','15','security','2026-06-30 09:05:00','2026-06-30 09:05:00'),
(1007,'SEED_session_lifetime_minutes','60','security','2026-06-30 09:06:00','2026-06-30 09:06:00'),
(1008,'SEED_pos_receipt_footer','Thank you for shopping with EZ SIMBS!','pos','2026-06-30 09:07:00','2026-06-30 09:07:00'),
(1009,'SEED_invoice_terms','Payment due within 15 days of invoice date.','general','2026-06-30 09:08:00','2026-06-30 09:08:00'),
(1010,'SEED_barcode_prefix','SEED','inventory','2026-06-30 09:09:00','2026-06-30 09:09:00'),
(1011,'SEED_sku_prefix','SEED-SKU-','inventory','2026-06-30 09:10:00','2026-06-30 09:10:00'),
(1012,'SEED_invoice_prefix','SEED-INV-','general','2026-06-30 09:11:00','2026-06-30 09:11:00'),
(1013,'SEED_po_prefix','SEED-PO-','general','2026-06-30 09:12:00','2026-06-30 09:12:00'),
(1014,'SEED_order_prefix','SEED-ORD-','general','2026-06-30 09:13:00','2026-06-30 09:13:00'),
(1015,'SEED_loyalty_points_per_taka','1','storefront','2026-06-30 09:14:00','2026-06-30 09:14:00'),
(1016,'SEED_loyalty_min_redeem','500','storefront','2026-06-30 09:15:00','2026-06-30 09:15:00'),
(1017,'SEED_free_shipping_threshold','2000','storefront','2026-06-30 09:16:00','2026-06-30 09:16:00'),
(1018,'SEED_default_shipping_fee','60','storefront','2026-06-30 09:17:00','2026-06-30 09:17:00'),
(1019,'SEED_test_mode','1','general','2026-06-30 09:18:00','2026-06-30 09:18:00'),
(1020,'SEED_support_email','support@ezsimbs.test','general','2026-06-30 09:19:00','2026-06-30 09:19:00');

-- ---------------------------------------------------------------------------
-- 25. banners  (20 rows - all created inactive on purpose.
--  There is no admin UI for banners, and the live landing page is driven by
--  the 2 existing ACTIVE banners, so the storefront stays visually unchanged.
--  Rows 1001/1002 document how to switch one on if you want to test rotation.)
-- ---------------------------------------------------------------------------
INSERT INTO banners (id, title, subtitle, image, link, position, sort_order, status, created_at) VALUES
(1001,'Seed Banner - Big Sale','Up to 50% off - this row is INACTIVE, activate it to test the hero slider.','assets/images/products/seed-01.svg','store.php','hero',10,'inactive','2026-06-30 10:00:00'),
(1002,'Seed Banner - New Arrivals','Fresh products just landed - INACTIVE by design.','assets/images/products/seed-02.svg','store.php','hero',20,'inactive','2026-06-30 10:01:00'),
(1003,'Seed Banner - Groceries','Everyday essentials at low prices.','assets/images/products/seed-03.svg','store.php?category=1','hero',30,'inactive','2026-06-30 10:02:00'),
(1004,'Seed Banner - Beverages','Cold drinks for hot days.','assets/images/products/seed-04.svg','store.php?category=1001','hero',40,'inactive','2026-06-30 10:03:00'),
(1005,'Seed Banner - Snacks','Snacks and chips bundle deals.','assets/images/products/seed-05.svg','store.php?category=1005','hero',50,'inactive','2026-06-30 10:04:00'),
(1006,'Seed Banner - Personal Care','Care for the whole family.','assets/images/products/seed-06.svg','store.php?category=1011','hero',60,'inactive','2026-06-30 10:05:00'),
(1007,'Seed Banner - Electronics','Phones and accessories at the best price.','assets/images/products/seed-07.svg','store.php?category=1017','hero',70,'inactive','2026-06-30 10:06:00'),
(1008,'Seed Banner - Dairy','Milk powder and frozen treats.','assets/images/products/seed-08.svg','store.php?category=1008','hero',80,'inactive','2026-06-30 10:07:00'),
(1009,'Seed Banner - Household','Cleaning supplies for a spotless home.','assets/images/products/seed-09.svg','store.php?category=1015','hero',90,'inactive','2026-06-30 10:08:00'),
(1010,'Seed Banner - Stationery','Notebooks and pens for the office.','assets/images/products/seed-10.svg','store.php?category=1019','hero',100,'inactive','2026-06-30 10:09:00'),
(1011,'Seed Banner - VIP Members','Exclusive discounts for VIP customers.','assets/images/products/seed-11.svg','dashboard.php','mid',110,'inactive','2026-06-30 10:10:00'),
(1012,'Seed Banner - Free Delivery','Free delivery on orders over 2000.','assets/images/products/seed-12.svg','store.php','mid',120,'inactive','2026-06-30 10:11:00'),
(1013,'Seed Banner - Loyalty Points','Earn points on every purchase.','assets/images/products/seed-13.svg','dashboard.php','mid',130,'inactive','2026-06-30 10:12:00'),
(1014,'Seed Banner - Mobile Accessories','Cases, earbuds and drives.','assets/images/products/seed-14.svg','store.php?category=1018','mid',140,'inactive','2026-06-30 10:13:00'),
(1015,'Seed Banner - Weekly Deals','Deals refreshed every Monday.','assets/images/products/seed-15.svg','store.php','mid',150,'inactive','2026-06-30 10:14:00'),
(1016,'Seed Banner - New Store Outlet','Now open in Banani.','assets/images/products/seed-16.svg','store.php','mid',160,'inactive','2026-06-30 10:15:00'),
(1017,'Seed Banner - Bulk Orders','Special rates on bulk buying.','assets/images/products/seed-17.svg','store.php','mid',170,'inactive','2026-06-30 10:16:00'),
(1018,'Seed Banner - Return Policy','7 day no-quibble returns.','assets/images/products/seed-18.svg','store.php','bottom',180,'inactive','2026-06-30 10:17:00'),
(1019,'Seed Banner - Support','Need help? We are on WhatsApp.','assets/images/products/seed-19.svg','dashboard.php','bottom',190,'inactive','2026-06-30 10:18:00'),
(1020,'Seed Banner - Thank You','See you again soon.','assets/images/products/seed-20.svg','store.php','bottom',200,'inactive','2026-06-30 10:19:00');

-- ---------------------------------------------------------------------------
-- 26. product_gallery  (20 rows - extra angles for products 1001-1020)
-- ---------------------------------------------------------------------------
INSERT INTO product_gallery (id, product_id, image, sort_order, created_at) VALUES
(1001,1001,'assets/images/products/seed-01.svg',1,'2026-06-30 11:00:00'),
(1002,1002,'assets/images/products/seed-02.svg',1,'2026-06-30 11:01:00'),
(1003,1003,'assets/images/products/seed-03.svg',1,'2026-06-30 11:02:00'),
(1004,1004,'assets/images/products/seed-04.svg',1,'2026-06-30 11:03:00'),
(1005,1005,'assets/images/products/seed-05.svg',1,'2026-06-30 11:04:00'),
(1006,1006,'assets/images/products/seed-06.svg',1,'2026-06-30 11:05:00'),
(1007,1007,'assets/images/products/seed-07.svg',1,'2026-06-30 11:06:00'),
(1008,1008,'assets/images/products/seed-08.svg',1,'2026-06-30 11:07:00'),
(1009,1009,'assets/images/products/seed-09.svg',1,'2026-06-30 11:08:00'),
(1010,1010,'assets/images/products/seed-10.svg',1,'2026-06-30 11:09:00'),
(1011,1011,'assets/images/products/seed-11.svg',1,'2026-06-30 11:10:00'),
(1012,1012,'assets/images/products/seed-12.svg',1,'2026-06-30 11:11:00'),
(1013,1013,'assets/images/products/seed-13.svg',1,'2026-06-30 11:12:00'),
(1014,1014,'assets/images/products/seed-14.svg',1,'2026-06-30 11:13:00'),
(1015,1015,'assets/images/products/seed-15.svg',1,'2026-06-30 11:14:00'),
(1016,1016,'assets/images/products/seed-16.svg',1,'2026-06-30 11:15:00'),
(1017,1017,'assets/images/products/seed-17.svg',1,'2026-06-30 11:16:00'),
(1018,1018,'assets/images/products/seed-18.svg',1,'2026-06-30 11:17:00'),
(1019,1019,'assets/images/products/seed-19.svg',1,'2026-06-30 11:18:00'),
(1020,1020,'assets/images/products/seed-20.svg',1,'2026-06-30 11:19:00');

-- ---------------------------------------------------------------------------
-- 27. wishlist  (20 rows - unique user/product pairs, customer accounts 1005,
--  1016 plus 1012, 1014 etc. so the storefront wishlist is not empty)
-- ---------------------------------------------------------------------------
INSERT INTO wishlist (id, user_id, product_id, created_at) VALUES
(1001,1005,1001,'2026-07-05 10:00:00'),
(1002,1005,1003,'2026-07-06 10:00:00'),
(1003,1005,1004,'2026-07-08 10:00:00'),
(1004,1005,1006,'2026-07-10 10:00:00'),
(1005,1016,1005,'2026-07-12 10:00:00'),
(1006,1016,1008,'2026-07-14 10:00:00'),
(1007,1016,1009,'2026-07-16 10:00:00'),
(1008,1016,1010,'2026-07-18 10:00:00'),
(1009,1012,1011,'2026-07-20 10:00:00'),
(1010,1012,1012,'2026-07-22 10:00:00'),
(1011,1012,1013,'2026-07-24 10:00:00'),
(1012,1012,1007,'2026-07-26 10:00:00'),
(1013,1014,1014,'2026-07-28 10:00:00'),
(1014,1014,1015,'2026-07-30 10:00:00'),
(1015,1014,1016,'2026-08-01 10:00:00'),
(1016,1017,1002,'2026-08-03 10:00:00'),
(1017,1017,1004,'2026-08-05 10:00:00'),
(1018,1017,1006,'2026-08-07 10:00:00'),
(1019,1017,1010,'2026-08-09 10:00:00'),
(1020,1017,1013,'2026-08-11 10:00:00');

-- ---------------------------------------------------------------------------
-- 28. cart  (20 rows - one cart per user)
--  Cart 1 already exists for user 5, so this table only adds users 1001-1020
--  (user 1005 is seed.customer, a different account from live user 5).
--  The UNIQUE key on user_id means one cart per account, so rows here map
--  1:1 onto users 1001-1020 and the bulk of them are intentionally EMPTY
--  (no cart_items) to represent an abandoned/cleared cart.
-- ---------------------------------------------------------------------------
INSERT INTO cart (id, user_id, created_at, updated_at) VALUES
(1001,1001,'2026-09-27 10:05:00','2026-09-27 10:05:00'),
(1002,1002,'2026-09-27 09:45:00','2026-09-27 09:45:00'),
(1003,1003,'2026-09-26 18:20:00','2026-09-26 18:20:00'),
(1004,1004,'2026-09-27 11:35:00','2026-09-27 11:35:00'),
(1005,1005,'2026-09-27 08:30:00','2026-09-27 10:45:00'),
(1006,1006,'2026-09-24 09:00:00','2026-09-24 09:00:00'),
(1007,1007,'2026-09-23 17:30:00','2026-09-23 17:30:00'),
(1008,1008,'2026-09-22 12:10:00','2026-09-22 12:10:00'),
(1009,1009,'2026-09-21 16:00:00','2026-09-21 16:00:00'),
(1010,1010,'2026-09-20 19:40:00','2026-09-20 19:40:00'),
(1011,1011,'2026-09-19 10:20:00','2026-09-19 10:20:00'),
(1012,1012,'2026-09-18 14:10:00','2026-09-18 14:10:00'),
(1013,1013,'2026-09-17 16:35:00','2026-09-17 16:35:00'),
(1014,1014,'2026-09-16 13:45:00','2026-09-16 13:45:00'),
(1015,1015,'2026-09-15 08:00:00','2026-09-15 08:00:00'),
(1016,1016,'2026-09-27 07:20:00','2026-09-27 09:15:00'),
(1017,1017,'2026-09-13 11:15:00','2026-09-13 11:15:00'),
(1018,1018,'2026-09-12 09:00:00','2026-09-12 09:00:00'),
(1019,1019,'2026-09-11 10:00:00','2026-09-11 10:00:00'),
(1020,1020,'2026-09-10 14:00:00','2026-09-10 14:00:00');

-- ---------------------------------------------------------------------------
-- 29. cart_items  (20 rows)
--  Spread across 5 active carts: 1005, 1006, 1012, 1014 and 1016.
--  The other 15 carts stay empty (cleared / abandoned carts).
-- ---------------------------------------------------------------------------
INSERT INTO cart_items (id, cart_id, product_id, qty, added_at) VALUES
(1001,1005,1001,2,'2026-09-27 08:30:00'),
(1002,1005,1003,1,'2026-09-27 08:31:00'),
(1003,1005,1006,3,'2026-09-27 08:33:00'),
(1004,1005,1010,1,'2026-09-27 08:35:00'),
(1005,1005,1013,2,'2026-09-27 08:36:00'),
(1006,1006,1002,6,'2026-09-24 09:00:00'),
(1007,1006,1007,4,'2026-09-24 09:02:00'),
(1008,1006,1008,1,'2026-09-24 09:04:00'),
(1009,1006,1009,2,'2026-09-24 09:05:00'),
(1010,1006,1011,1,'2026-09-24 09:07:00'),
(1011,1012,1004,2,'2026-09-18 14:10:00'),
(1012,1012,1012,2,'2026-09-18 14:12:00'),
(1013,1012,1013,1,'2026-09-18 14:14:00'),
(1014,1012,1015,1,'2026-09-18 14:15:00'),
(1015,1012,1016,3,'2026-09-18 14:17:00'),
(1016,1014,1003,2,'2026-09-16 13:45:00'),
(1017,1014,1005,4,'2026-09-16 13:47:00'),
(1018,1014,1014,1,'2026-09-16 13:49:00'),
(1019,1016,1001,1,'2026-09-27 07:20:00'),
(1020,1016,1006,2,'2026-09-27 07:22:00');

-- ---------------------------------------------------------------------------
-- 30. product_reviews  (20 rows - all 3 statuses, ratings 1-5)
--  unique (user_id, product_id) is respected: no duplicate pairs.
-- ---------------------------------------------------------------------------
INSERT INTO product_reviews (id, product_id, user_id, rating, title, comment, status, created_at) VALUES
(1001,1001,1005,5,'Great taste and price','Chilled and fresh. Will buy again.','approved','2026-07-06 11:00:00'),
(1002,1002,1016,4,'Good but pricey','Tastes fine, price is on the higher side.','approved','2026-07-08 11:00:00'),
(1003,1003,1005,5,'Energy boost works','Best pick for late night shifts.','approved','2026-07-10 11:00:00'),
(1004,1004,1012,4,'Rich chocolate taste','Kids love it. A bit sweet for me.','approved','2026-07-12 11:00:00'),
(1005,1005,1014,5,'Real mango flavour','Not too sweet, tastes like actual mango.','approved','2026-07-14 11:00:00'),
(1006,1006,1005,3,'Chips are okay','Fine for the price but quite salty.','pending','2026-07-16 11:00:00'),
(1007,1007,1017,4,'Good snack mix','The chanachur mix is properly spicy.','approved','2026-07-18 11:00:00'),
(1008,1008,1016,5,'Milk powder is top quality','Dissolves smoothly, no lumps.','approved','2026-07-20 11:00:00'),
(1009,1009,1012,2,'Leaked in the bag','Bottle cap was loose, half the oil leaked.','rejected','2026-07-22 11:00:00'),
(1010,1010,1005,4,'Fresh breath','Works well, tube lasts about a month.','approved','2026-07-24 11:00:00'),
(1011,1011,1014,3,'Soap is fine','Nothing special but the pack price is good.','approved','2026-07-26 11:00:00'),
(1012,1012,1005,5,'Germ free feeling','Trustworthy brand, no complaints.','approved','2026-07-28 11:00:00'),
(1013,1013,1016,4,'Hand wash is strong','Little goes a long way.','approved','2026-07-30 11:00:00'),
(1014,1014,1012,5,'Excellent phone','Battery and camera are great for the price.','approved','2026-08-01 11:00:00'),
(1015,1015,1014,4,'Sound is good','Earbuds are comfortable for long use.','approved','2026-08-03 11:00:00'),
(1016,1016,1017,5,'Fast and reliable','Speeds are good, casing is sturdy.','approved','2026-08-05 11:00:00'),
(1017,1017,1012,1,'Pages came loose','Binding was already broken when delivered.','rejected','2026-08-07 11:00:00'),
(1018,1018,1016,4,'Pens write smoothly','Ink is dark, no smudging.','approved','2026-08-09 11:00:00'),
(1019,1019,1014,2,'Stopped working in a month','Mouse sensor stopped tracking.','pending','2026-08-11 11:00:00'),
(1020,1020,1017,4,'Nice gift hamper','Good presentation, packaging was solid.','approved','2026-08-13 11:00:00');

-- ---------------------------------------------------------------------------
-- 31. coupons  (20 rows - percent + fixed, active/inactive/expired/exhausted)
--  codes are SEED_ prefixed so they never clash with live storefront codes.
-- ---------------------------------------------------------------------------
INSERT INTO coupons (id, code, description, type, value, min_order, max_uses, used_count, starts_at, expires_at, status, created_at) VALUES
(1001,'SEEDWELCOME20','20% off your first order','percent',20.00,500.00,100,3,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 12:00:00'),
(1002,'SEEDWELCOME50','50% off for new customers','percent',50.00,1000.00,50,1,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 12:01:00'),
(1003,'SEEDFLAT100','Flat 100 off on orders over 800','fixed',100.00,800.00,200,7,'2026-07-01 00:00:00','2026-11-30 23:59:59','active','2026-06-30 12:02:00'),
(1004,'SEEDFLAT250','Flat 250 off on orders over 2000','fixed',250.00,2000.00,150,12,'2026-07-01 00:00:00','2026-11-30 23:59:59','active','2026-06-30 12:03:00'),
(1005,'SEEDBEVER5','5% off on beverages','percent',5.00,200.00,300,22,'2026-07-01 00:00:00','2026-10-31 23:59:59','active','2026-06-30 12:04:00'),
(1006,'SEEDSNACK10','10% off on snacks','percent',10.00,300.00,300,18,'2026-07-01 00:00:00','2026-10-31 23:59:59','active','2026-06-30 12:05:00'),
(1007,'SEEDCARE15','15% off on personal care','percent',15.00,500.00,200,9,'2026-07-01 00:00:00','2026-12-15 23:59:59','active','2026-06-30 12:06:00'),
(1008,'SEEDELEC200','Flat 200 off on electronics over 3000','fixed',200.00,3000.00,80,4,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 12:07:00'),
(1009,'SEEDVIP25','25% off for VIP customers','percent',25.00,1000.00,60,15,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 12:08:00'),
(1010,'SEEDFREESHIP','Free shipping on any order','fixed',60.00,0.00,500,40,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 12:09:00'),
(1011,'SEEDBULK300','Flat 300 off on bulk orders over 5000','fixed',300.00,5000.00,40,2,'2026-07-01 00:00:00','2026-11-30 23:59:59','active','2026-06-30 12:10:00'),
(1012,'SEEDSTUDENT12','12% student discount','percent',12.00,400.00,100,5,'2026-08-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 12:11:00'),
(1013,'SEEDEID30','30% off for Eid Dhan','percent',30.00,1500.00,40,38,'2026-03-01 00:00:00','2026-04-15 23:59:59','inactive','2026-02-20 12:00:00'),
(1014,'SEEDNEWYEAR30','30% off for New Year sale','percent',30.00,1000.00,40,25,'2026-01-01 00:00:00','2026-01-31 23:59:59','inactive','2025-12-20 12:00:00'),
(1015,'SEEDEXPIRED10','Expired test coupon','percent',10.00,500.00,100,0,'2026-01-01 00:00:00','2026-02-01 23:59:59','inactive','2025-12-25 12:00:00'),
(1016,'SEEDEXHAUSTED','Fully used test coupon','percent',40.00,2000.00,5,5,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 12:15:00'),
(1017,'SEEDFUTURE20','Not started yet','percent',20.00,1000.00,50,0,'2027-01-01 00:00:00','2027-03-31 23:59:59','active','2026-06-30 12:16:00'),
(1018,'SEEDNOEXPIRY18','18% off, no end date','percent',18.00,600.00,0,11,'2026-07-01 00:00:00',NULL,'active','2026-06-30 12:17:00'),
(1019,'SEEDTESTONLY','Test coupon - do not advertise','fixed',10.00,100.00,10,2,'2026-07-01 00:00:00','2026-10-01 23:59:59','active','2026-06-30 12:18:00'),
(1020,'SEEDDISABLED50','Disabled coupon - validation test','percent',50.00,100.00,100,0,'2026-07-01 00:00:00','2026-12-31 23:59:59','inactive','2026-06-30 12:19:00');

-- ---------------------------------------------------------------------------
-- 32. orders  (20 rows - all 7 statuses, storefront + 2 POS-originated orders
--  linked back to the seeded sales via sale_id)
-- ---------------------------------------------------------------------------
INSERT INTO orders (id, order_no, user_id, customer_id, subtotal, discount, coupon_code, tax, shipping, grand_total, payment_method, status, shipping_address, contact_phone, notes, is_pos, sale_id, created_at, updated_at) VALUES
(1001,'SEED-ORD-20260810-1001',1005,1001,195.00,0.00,NULL,19.50,50.00,264.50,'cod','delivered','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Delivered on the same day.','0',NULL,'2026-08-10 09:20:00','2026-08-11 17:00:00'),
(1002,'SEED-ORD-20260812-1002',1005,1001,480.00,96.00,'SEEDWELCOME20',38.40,0.00,422.40,'mobile','delivered','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Welcome coupon applied.','0',NULL,'2026-08-12 10:05:00','2026-08-13 16:30:00'),
(1003,'SEED-ORD-20260815-1003',1016,NULL,570.00,0.00,NULL,57.00,60.00,687.00,'cod','delivered','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,'0',NULL,'2026-08-15 12:40:00','2026-08-16 15:00:00'),
(1004,'SEED-ORD-20260818-1004',1016,NULL,185.00,0.00,NULL,18.50,60.00,263.50,'cod','delivered','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,'0',NULL,'2026-08-18 14:10:00','2026-08-19 12:00:00'),
(1005,'SEED-ORD-20260821-1005',1016,NULL,100.00,5.00,'SEEDBEVER5',9.50,60.00,164.50,'card','delivered','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,'0',NULL,'2026-08-21 09:15:00','2026-08-22 11:45:00'),
(1006,'SEED-ORD-20260824-1006',1005,1001,890.00,133.50,'SEEDCARE15',75.65,0.00,832.15,'mobile','delivered','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001',NULL,'0',NULL,'2026-08-24 18:00:00','2026-08-26 10:30:00'),
(1007,'SEED-ORD-20260827-1007',1005,1001,4900.00,0.00,NULL,490.00,0.00,5390.00,'cod','returned','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Earbuds returned - pairing issue.','0',NULL,'2026-08-27 20:15:00','2026-09-02 14:20:00'),
(1008,'SEED-ORD-20260830-1008',1016,NULL,1250.00,0.00,NULL,125.00,0.00,1375.00,'cod','delivered','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011','Delivered 2026-09-01; the flash drive was later returned as defective.','0',NULL,'2026-08-30 11:00:00','2026-09-01 09:00:00'),
(1009,'SEED-ORD-20260902-1009',1005,1001,62.00,0.00,NULL,6.20,60.00,128.20,'cod','shipped','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001',NULL,'0',NULL,'2026-09-02 15:30:00','2026-09-03 08:45:00'),
(1010,'SEED-ORD-20260904-1010',1016,NULL,480.00,0.00,NULL,48.00,60.00,588.00,'cod','shipped','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,'0',NULL,'2026-09-04 10:50:00','2026-09-05 09:30:00'),
(1011,'SEED-ORD-20260906-1011',1005,1001,210.00,0.00,NULL,21.00,60.00,291.00,'mobile','processing','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Packed, awaiting courier pickup.','0',NULL,'2026-09-06 16:20:00','2026-09-07 10:00:00'),
(1012,'SEED-ORD-20260908-1012',1016,NULL,165.00,0.00,NULL,16.50,60.00,241.50,'cod','processing','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,'0',NULL,'2026-09-08 13:35:00','2026-09-09 09:00:00'),
(1013,'SEED-ORD-20260910-1013',1005,1001,1250.00,0.00,NULL,125.00,0.00,1375.00,'cod','confirmed','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Payment confirmed by COD collection.','0',NULL,'2026-09-10 09:00:00','2026-09-10 12:00:00'),
(1014,'SEED-ORD-20260911-1014',1016,NULL,25.00,0.00,NULL,2.50,60.00,87.50,'cod','cancelled','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011','Cancelled by customer within 1 hour.','0',NULL,'2026-09-11 11:10:00','2026-09-11 12:00:00'),
(1015,'SEED-ORD-20260912-1015',1005,1001,95.00,0.00,NULL,9.50,60.00,164.50,'mobile','cancelled','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Out of stock at the time of picking.','0',NULL,'2026-09-12 17:25:00','2026-09-13 10:00:00'),
(1016,'SEED-ORD-20260913-1016',1005,1001,4900.00,980.00,'SEEDWELCOME20',392.00,0.00,4312.00,'card','pending','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Awaiting payment confirmation.','0',NULL,'2026-09-13 19:00:00','2026-09-13 19:00:00'),
(1017,'SEED-ORD-20260914-1017',1016,NULL,72.00,0.00,NULL,7.20,60.00,139.20,'cod','pending','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011','New order, not yet processed.','0',NULL,'2026-09-14 08:20:00','2026-09-14 08:20:00'),
(1018,'SEED-ORD-20260915-1018',1005,1001,1850.00,0.00,NULL,185.00,0.00,2035.00,'card','processing','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001',NULL,'0',NULL,'2026-09-15 14:00:00','2026-09-16 09:00:00'),
(1019,'SEED-POS-20260916-1019',1004,1005,890.00,40.00,NULL,85.00,0.00,935.00,'card','delivered','House 9, Uttara Sector 4, Dhaka-1230','+8801811000005','POS order mirrored from SEED-INV-20260916-0015.','1',1015,'2026-09-16 16:00:00','2026-09-16 16:05:00'),
(1020,'SEED-POS-20260925-1020',1004,1008,375.00,25.00,NULL,35.00,0.00,385.00,'cash','cancelled',NULL,NULL,'POS order mirrored from SEED-INV-20260925-0019 (walk-in, cancelled sale).','1',1019,'2026-09-25 15:10:00','2026-09-25 15:20:00');

-- ---------------------------------------------------------------------------
-- 33. order_items  (20 rows)
--  Orders 1011-1018 each get 1 line; the delivered ones get 1-2 lines.
-- ---------------------------------------------------------------------------
INSERT INTO order_items (id, order_id, product_id, qty, unit_price, total) VALUES
(1001,1001,1001,3,65.00,195.00),
(1002,1002,1004,1,480.00,480.00),
(1003,1003,1005,6,95.00,570.00),
(1004,1004,1010,1,185.00,185.00),
(1005,1005,1001,2,50.00,100.00),
(1006,1006,1008,1,890.00,890.00),
(1007,1007,1015,2,2450.00,4900.00),
(1008,1008,1016,1,1250.00,1250.00),
(1009,1009,1002,1,62.00,62.00),
(1010,1010,1004,1,480.00,480.00),
(1011,1011,1013,1,210.00,210.00),
(1012,1012,1009,1,165.00,165.00),
(1013,1013,1016,1,1250.00,1250.00),
(1014,1014,1006,1,25.00,25.00),
(1015,1015,1005,1,95.00,95.00),
(1016,1016,1015,2,2450.00,4900.00),
(1017,1017,1012,1,72.00,72.00),
(1018,1018,1014,10,185.00,1850.00),
(1019,1019,1008,1,890.00,890.00),
(1020,1020,1006,15,25.00,375.00);

-- ---------------------------------------------------------------------------
-- 34. sale_returns  (20 rows - Phase 13 customer returns & refunds)
--  Every status is present: 12 refunded, 4 requested, 2 rejected, 1 approved,
--  1 cancelled. Money follows SaleReturn::computeRefund():
--    refund = R - discount*(R/S) + tax*(R/S) + shipping*(R/S)
--  where R = returned line value (sale_returns.subtotal) and S = the original
--  sale/order subtotal. discount/tax/shipping below are the reversed shares,
--  so refund_total = subtotal - discount + tax + shipping EXACTLY (check 14).
--  Seed return_no values use the SEED_RET- prefix (RET- is the live format,
--  generated by generateReturnNo()).
--  Timeline notes:
--    * POS returns start 'approved' at the counter; the demo records the
--      cashier (user 10) as approved_by for a full audit trail.
--    * Online requests start 'requested' (require_approval=1); decided ones
--      are approved/rejected by the admin (user 1) with an admin_note.
--    * 1008 and 1020 both hit sale 1020 (grand 715.00): 476.67 + 238.33 is
--      the ceiling clamp - the second refund stops exactly at the remainder.
--    * 1018 was VOIDED after auto-approval, so it carries no received/refunded
--      stamps and its sale_line tally reverted to 0 (stock never returned).
--    * Online returns leave branch_id = 1 (default fulfilment branch).
-- ---------------------------------------------------------------------------
INSERT INTO sale_returns (id, return_no, type, sale_id, order_id, customer_id, branch_id, status, reason_code, reason, method, subtotal, discount, tax, shipping, refund_total, loyalty_reversed, requested_by, requested_for, approved_by, approved_at, received_by, received_at, refunded_by, refunded_at, admin_note, created_at) VALUES
(1001,'SEED-RET-1001','pos',1001,NULL,1001,1,'refunded','damaged',NULL,'original',195.00,0.00,19.50,0.00,214.50,21,10,NULL,10,'2026-08-02 11:00:00',10,'2026-08-02 11:10:00',10,'2026-08-02 11:12:00',NULL,'2026-08-02 11:00:00'),
(1002,'SEED-RET-1002','pos',1002,NULL,1002,1,'refunded','changed_mind',NULL,'original',62.00,2.00,6.00,0.00,66.00,6,10,NULL,10,'2026-08-15 13:00:00',10,'2026-08-15 13:10:00',10,'2026-08-15 13:15:00',NULL,'2026-08-15 13:00:00'),
(1003,'SEED-RET-1003','online',NULL,1005,NULL,1,'rejected','changed_mind',NULL,'original',100.00,5.00,9.50,60.00,164.50,0,NULL,1016,1,'2026-08-28 14:00:00',NULL,NULL,NULL,NULL,'Customer never sent the goods back; request rejected.', '2026-08-25 10:00:00'),
(1004,'SEED-RET-1004','online',NULL,1002,1001,1,'requested','damaged',NULL,'original',480.00,96.00,38.40,0.00,422.40,0,NULL,1005,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-16 11:00:00'),
(1005,'SEED-RET-1005','online',NULL,1004,NULL,1,'requested','wrong_item',NULL,'original',185.00,0.00,18.50,60.00,263.50,0,NULL,1016,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-20 12:00:00'),
(1006,'SEED-RET-1006','pos',1014,NULL,1004,1,'refunded','not_as_described',NULL,'original',480.00,0.00,48.00,0.00,528.00,52,10,NULL,10,'2026-09-16 10:00:00',10,'2026-09-16 10:30:00',10,'2026-09-16 10:45:00',NULL,'2026-09-16 10:00:00'),
(1007,'SEED-RET-1007','pos',1007,NULL,1009,1,'refunded','defective',NULL,'original',165.00,15.00,15.00,0.00,165.00,16,10,NULL,10,'2026-08-24 09:00:00',10,'2026-08-24 09:20:00',10,'2026-08-24 09:30:00',NULL,'2026-08-24 09:00:00'),
(1008,'SEED-RET-1008','pos',1020,NULL,1009,1,'refunded','defective',NULL,'original',420.00,20.00,40.00,36.67,476.67,47,10,NULL,10,'2026-09-28 09:30:00',10,'2026-09-28 09:45:00',10,'2026-09-28 10:00:00',NULL,'2026-09-28 09:30:00'),
(1009,'SEED-RET-1009','pos',1006,NULL,1006,1,'refunded','other',NULL,'original',285.00,0.00,28.50,0.00,313.50,31,10,NULL,10,'2026-08-23 15:00:00',10,'2026-08-23 15:30:00',10,'2026-08-23 15:40:00',NULL,'2026-08-23 15:00:00'),
(1010,'SEED-RET-1010','pos',1010,NULL,1011,1,'refunded','defective',NULL,'original',576.00,0.00,57.60,0.00,633.60,63,10,NULL,10,'2026-09-05 11:00:00',10,'2026-09-05 11:30:00',10,'2026-09-05 11:40:00',NULL,'2026-09-05 11:00:00'),
(1011,'SEED-RET-1011','pos',1009,NULL,1010,1,'refunded','other',NULL,'original',576.00,76.00,50.00,0.00,550.00,55,10,NULL,10,'2026-09-10 10:00:00',10,'2026-09-10 10:15:00',10,'2026-09-10 10:45:00',NULL,'2026-09-10 10:00:00'),
(1012,'SEED-RET-1012','pos',1003,NULL,1003,1,'refunded','damaged',NULL,'original',25.00,0.00,2.50,6.25,33.75,3,10,NULL,10,'2026-08-08 12:00:00',10,'2026-08-09 11:00:00',10,'2026-08-09 11:20:00',NULL,'2026-08-08 12:00:00'),
(1013,'SEED-RET-1013','online',NULL,1003,NULL,1,'requested','not_as_described',NULL,'original',190.00,0.00,19.00,20.00,229.00,0,NULL,1016,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-20 09:00:00'),
(1014,'SEED-RET-1014','online',NULL,1001,1001,1,'rejected','damaged',NULL,'original',65.00,0.00,6.50,16.67,88.17,0,NULL,1005,1,'2026-08-16 10:00:00',NULL,NULL,NULL,NULL,'Damaged at delivery - refund declined; replacement issued via support ticket.', '2026-08-15 11:00:00'),
(1015,'SEED-RET-1015','online',NULL,1006,1001,1,'refunded','defective',NULL,'original',890.00,133.50,75.65,0.00,832.15,0,NULL,1005,1,'2026-08-27 10:00:00',10,'2026-08-28 10:30:00',10,'2026-08-28 11:00:00',NULL,'2026-08-27 10:00:00'),
(1016,'SEED-RET-1016','pos',1014,NULL,1004,1,'approved','not_as_described',NULL,'original',480.00,0.00,48.00,0.00,528.00,0,10,NULL,10,'2026-09-18 10:30:00',NULL,NULL,NULL,NULL,NULL,'2026-09-18 10:30:00'),
(1017,'SEED-RET-1017','pos',1017,NULL,1005,1,'refunded','damaged',NULL,'original',260.00,10.00,25.00,0.00,275.00,27,10,NULL,10,'2026-09-22 09:00:00',10,'2026-09-22 09:15:00',10,'2026-09-22 09:30:00',NULL,'2026-09-22 09:00:00'),
(1018,'SEED-RET-1018','pos',1018,NULL,1006,1,'cancelled','other',NULL,'original',186.00,0.00,18.60,40.00,244.60,0,10,NULL,10,'2026-09-24 10:00:00',NULL,NULL,NULL,NULL,'VOIDED: duplicate return keyed in error.', '2026-09-24 10:00:00'),
(1019,'SEED-RET-1019','online',NULL,1008,NULL,1,'requested','defective',NULL,'original',1250.00,0.00,125.00,0.00,1375.00,0,NULL,1016,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-05 15:00:00'),
(1020,'SEED-RET-1020','pos',1020,NULL,1009,1,'refunded','not_as_described',NULL,'original',210.00,10.00,20.00,18.33,238.33,23,10,NULL,10,'2026-09-30 09:00:00',10,'2026-09-30 09:15:00',10,'2026-09-30 09:30:00',NULL,'2026-09-30 09:00:00');

-- ---------------------------------------------------------------------------
-- 35. sale_return_items  (22 rows - ids 1001-1022, one line per return)
--  1021/1022 are OUTSIDE the 1001-1020 verification window on purpose (the
--  extra lines of return 1011, which returns the 3-line split of sale 1009).
--  sale_item_id is set for POS returns, order_item_id for online ones; qty is
--  always positive. restock is the receive() decision: 1 = sellable (a matching
--  stock_logs row exists - rows 1018 / 1021 / 1022 / 1023), 0 = damaged stock
--  that never re-entered inventory.
-- ---------------------------------------------------------------------------
INSERT INTO sale_return_items (id, return_id, sale_item_id, order_item_id, product_id, qty, unit_price, discount, total, restock, condition_note) VALUES
(1001,1001,1001,NULL,1001,3,65.00,0.00,195.00,0,'Lids dented - write off.'),
(1002,1002,1002,NULL,1002,1,62.00,0.00,62.00,0,'Opened, half consumed.'),
(1003,1003,NULL,1005,1001,2,50.00,0.00,100.00,0,NULL),
(1004,1004,NULL,1002,1004,1,480.00,0.00,480.00,0,NULL),
(1005,1005,NULL,1004,1010,1,185.00,0.00,185.00,0,NULL),
(1006,1006,1014,NULL,1004,1,480.00,0.00,480.00,0,'Dented can.'),
(1007,1007,1007,NULL,1009,1,165.00,0.00,165.00,0,'Bottle marked defective.'),
(1008,1008,1020,NULL,1013,2,210.00,0.00,420.00,0,'Both bottles leaked.'),
(1009,1009,1006,NULL,1005,3,95.00,0.00,285.00,0,'Packs torn.'),
(1010,1010,1010,NULL,1012,8,72.00,0.00,576.00,1,'Sealed - resellable.'),
(1011,1011,1009,NULL,1011,4,48.00,0.00,192.00,1,'Crate A resellable.'),
(1021,1011,1021,NULL,1011,4,48.00,0.00,192.00,1,'Crate B resellable.'),
(1022,1011,1022,NULL,1011,4,48.00,0.00,192.00,0,'Crate C damaged - write off.'),
(1012,1012,1003,NULL,1006,1,25.00,0.00,25.00,1,'Single pack resellable.'),
(1013,1013,NULL,1003,1005,2,95.00,0.00,190.00,0,NULL),
(1014,1014,NULL,1001,1001,1,65.00,0.00,65.00,0,NULL),
(1015,1015,NULL,1006,1008,1,890.00,0.00,890.00,0,'Unit does not power on.'),
(1016,1016,1014,NULL,1004,1,480.00,0.00,480.00,0,NULL),
(1017,1017,1017,NULL,1001,4,65.00,0.00,260.00,0,'Bottles leaking.'),
(1018,1018,1018,NULL,1002,3,62.00,0.00,186.00,0,NULL),
(1019,1019,NULL,1008,1016,1,1250.00,0.00,1250.00,0,NULL),
(1020,1020,1020,NULL,1013,1,210.00,0.00,210.00,0,'Third bottle slightly damaged.');

-- ---------------------------------------------------------------------------
-- 36. sale_return_payments  (12 rows - the refund LEDGER, one row per REFUNDED
--  return; requested / approved / rejected / cancelled RMAs pay nothing. This
--  child table intentionally has fewer than 20 rows: it reflects how many
--  refunds actually happened (12 of 20 returns), so check 1 reports 12 here.
--  methods match what actually left the till (the tender the sale was paid
--  with - see check 14/15 for the money agreement).
-- ---------------------------------------------------------------------------
INSERT INTO sale_return_payments (id, return_id, amount, method, reference, notes, processed_by, created_at) VALUES
(1001,1001,214.50,'cash',NULL,NULL,10,'2026-08-02 11:12:00'),
(1002,1002,66.00,'card','REFCARD-1002',NULL,10,'2026-08-15 13:15:00'),
(1003,1006,528.00,'cash',NULL,NULL,10,'2026-09-16 10:45:00'),
(1004,1007,165.00,'mobile','REFMOB-1007',NULL,10,'2026-08-24 09:30:00'),
(1005,1008,476.67,'mobile','REFMOB-1008','First refund on SEED-INV-20260927-0020 (2 of 3 units).',10,'2026-09-28 10:00:00'),
(1006,1009,313.50,'cash',NULL,NULL,10,'2026-08-23 15:40:00'),
(1007,1010,633.60,'mobile','REFMOB-1010',NULL,10,'2026-09-05 11:40:00'),
(1008,1011,550.00,'cash',NULL,NULL,10,'2026-09-10 10:45:00'),
(1009,1012,33.75,'mobile','REFMOB-1012',NULL,10,'2026-08-09 11:20:00'),
(1010,1015,832.15,'card','REFCARD-1015','Card refund to the original payment card.',10,'2026-08-28 11:00:00'),
(1011,1017,275.00,'cash',NULL,NULL,10,'2026-09-22 09:30:00'),
(1012,1020,238.33,'mobile','REFMOB-1020','Second refund on SEED-INV-20260927-0020 - closes the remaining balance.',10,'2026-09-30 09:30:00');

-- ---------------------------------------------------------------------------
-- 37. return_photos  (20 rows - request-level evidence for customer RMAs)
--  return_item_id stays NULL (request-level). Paths are synthetic placeholders
--  that keep the view.php photo grid testable; 5 photos on the photo-case
--  return 1019 and the rest spread over the online requests/rejects 1003,
--  1004, 1005, 1014, 1015 so every online uploader is represented.
-- ---------------------------------------------------------------------------
INSERT INTO return_photos (id, return_kind, return_id, return_item_id, path, uploaded_by, created_at) VALUES
(1001,'sale',1003,NULL,'uploads/returns/seed-1003-1.jpg',1016,'2026-08-25 10:05:00'),
(1002,'sale',1003,NULL,'uploads/returns/seed-1003-2.jpg',1016,'2026-08-25 10:06:00'),
(1003,'sale',1003,NULL,'uploads/returns/seed-1003-3.jpg',1016,'2026-08-25 10:07:00'),
(1004,'sale',1004,NULL,'uploads/returns/seed-1004-1.jpg',1005,'2026-08-16 11:05:00'),
(1005,'sale',1004,NULL,'uploads/returns/seed-1004-2.jpg',1005,'2026-08-16 11:06:00'),
(1006,'sale',1004,NULL,'uploads/returns/seed-1004-3.jpg',1005,'2026-08-16 11:07:00'),
(1007,'sale',1005,NULL,'uploads/returns/seed-1005-1.jpg',1016,'2026-08-20 12:05:00'),
(1008,'sale',1005,NULL,'uploads/returns/seed-1005-2.jpg',1016,'2026-08-20 12:06:00'),
(1009,'sale',1005,NULL,'uploads/returns/seed-1005-3.jpg',1016,'2026-08-20 12:07:00'),
(1010,'sale',1014,NULL,'uploads/returns/seed-1014-1.jpg',1005,'2026-08-15 11:05:00'),
(1011,'sale',1014,NULL,'uploads/returns/seed-1014-2.jpg',1005,'2026-08-15 11:06:00'),
(1012,'sale',1014,NULL,'uploads/returns/seed-1014-3.jpg',1005,'2026-08-15 11:07:00'),
(1013,'sale',1015,NULL,'uploads/returns/seed-1015-1.jpg',1005,'2026-08-27 10:05:00'),
(1014,'sale',1015,NULL,'uploads/returns/seed-1015-2.jpg',1005,'2026-08-27 10:06:00'),
(1015,'sale',1015,NULL,'uploads/returns/seed-1015-3.jpg',1005,'2026-08-27 10:07:00'),
(1016,'sale',1019,NULL,'uploads/returns/seed-1019-1.jpg',1016,'2026-09-05 15:05:00'),
(1017,'sale',1019,NULL,'uploads/returns/seed-1019-2.jpg',1016,'2026-09-05 15:06:00'),
(1018,'sale',1019,NULL,'uploads/returns/seed-1019-3.jpg',1016,'2026-09-05 15:07:00'),
(1019,'sale',1019,NULL,'uploads/returns/seed-1019-4.jpg',1016,'2026-09-05 15:08:00'),
(1020,'sale',1019,NULL,'uploads/returns/seed-1019-5.jpg',1016,'2026-09-05 15:09:00');

-- ---------------------------------------------------------------------------
-- 20b. stock_logs, Phase-14 outbound rows  (18 rows - ids 1024-1041)
--  OUTSIDE the 1001-1020 verification window on purpose (check 1 counts 20).
--  1024-1040 are the 'purchase_return' OUT rows Inventory::recordPurchaseReturn
--  writes at receive() time for every shipped-back return (reference_id = the
--  RETURN id). 1041 is the 'purchase_return_void' IN row Inventory reacts with
--  when return 1017 is voided - the stock that went out came back in again.
--  Credit total per return = subtotal + tax (check 18); here every unit was
--  shipped, so stock rows mirror the return line qty exactly.
-- ---------------------------------------------------------------------------
INSERT INTO stock_logs (id, product_id, branch_id, type, qty, reference_id, reference_type, notes, user_id, created_at) VALUES
(1024,1001,1,'purchase_return',-10,1001,'purchase_return','Shipped back against SEED-PRET-1001.',1,'2026-08-02 11:10:00'),
(1025,1006,1,'purchase_return',-20,1002,'purchase_return','Shipped back against SEED-PRET-1002.',1,'2026-08-16 10:00:00'),
(1026,1003,1,'purchase_return',-5,1003,'purchase_return','Shipped back against SEED-PRET-1003.',1,'2026-08-20 12:10:00'),
(1027,1009,1,'purchase_return',-6,1004,'purchase_return','Shipped back against SEED-PRET-1004.',1,'2026-08-22 10:00:00'),
(1028,1013,2,'purchase_return',-4,1005,'purchase_return','Shipped back against SEED-PRET-1005.',1,'2026-08-25 09:00:00'),
(1029,1010,2,'purchase_return',-6,1006,'purchase_return','Shipped back against SEED-PRET-1006.',1,'2026-08-25 09:30:00'),
(1030,1014,1,'purchase_return',-1,1007,'purchase_return','Shipped back against SEED-PRET-1007.',1,'2026-09-10 10:00:00'),
(1031,1015,1,'purchase_return',-3,1008,'purchase_return','Shipped back against SEED-PRET-1008.',1,'2026-09-12 12:00:00'),
(1032,1016,1,'purchase_return',-4,1009,'purchase_return','Shipped back against SEED-PRET-1009.',1,'2026-09-12 12:30:00'),
(1033,1008,3,'purchase_return',-2,1010,'purchase_return','Shipped back against SEED-PRET-1010.',1,'2026-09-20 09:30:00'),
(1034,1003,1,'purchase_return',-4,1011,'purchase_return','Shipped back against SEED-PRET-1011.',1,'2026-09-01 10:00:00'),
(1035,1005,1,'purchase_return',-66,1012,'purchase_return','Shipped back against SEED-PRET-1012.',1,'2026-09-05 11:00:00'),
(1036,1006,1,'purchase_return',-2,1015,'purchase_return','Partial return shipped back against SEED-PRET-1015.',1,'2026-09-01 15:00:00'),
(1037,1009,1,'purchase_return',-15,1016,'purchase_return','Shipped back against SEED-PRET-1016.',1,'2026-09-08 14:00:00'),
(1038,1008,3,'purchase_return',-2,1018,'purchase_return','Shipped back against SEED-PRET-1018.',1,'2026-09-21 10:30:00'),
(1039,1003,1,'purchase_return',-3,1019,'purchase_return','Shipped back against SEED-PRET-1019.',1,'2026-09-25 11:00:00'),
(1040,1001,1,'purchase_return',-90,1020,'purchase_return','Ceiling-clamp return shipped back against SEED-PRET-1020.',1,'2026-09-27 12:00:00'),
(1041,1009,1,'in',5,1017,'purchase_return_void','Stock restored after SEED-PRET-1017 was voided.',1,'2026-09-24 12:00:00');

-- ---------------------------------------------------------------------------
-- 38. purchase_returns  (20 rows - Phase 14 supplier returns, ids 1001-1020)
--  return_no uses the SEED-PRET- prefix (PRET- is the generateReturnNo('purchase')
--  live format). Five of the six statuses are present: 15 credited, 1 requested,
--  1 rejected, 2 received (shipped but not yet credited), 1 cancelled (voided).
--  Money follows PurchaseReturn::computeCredit() at the purchase line's unit_cost:
--    credit_total = subtotal + tax  (check 18), with 1012 hitting the split
--    applied_to_due + to_balance path and 1020 the exact ceiling clamp on
--    PO 1001 (528.00 + 4752.00 = 5280.00 = PO total).
--  The awkward cases at a glance:
--    1011  credit fully absorbed by PO 1014's due (applied 814, to_balance 0)
--    1012  split credit on PO 1003 (66u of the approved half-paid line):
--          applied_to_due 4752.00 wipes the PO's due, 475.20 overflows to
--          balance. The purchase line keeps its legacy received_qty = 0 default,
--          so the credit state (sections 38-40) is the source of truth here.
--    1013  requested by seed.branch@ (user 1003, branch 3) on PO 1013 -
--          needs approval; coherent with the branch-scoped returns list
--    1014  rejected with an admin_note explaining why
--    1015  partial return - only 2 of the received units on one line
--    1016  received (shipped back); stock already left via stock_logs 1037
--    1017  VOIDED after receiving - returned_qty freed, stock restored (1041)
--    1018  carries photo evidence (return_photos rows 1021-1023)
--    1019  credited return on an overpaid PO (1004) leaving a balance credit
--    1020  credit hits the exact ceiling clamp on PO 1001
--  Only 15 of the 20 are credited, so purchase_return_payments has 15 rows.
--  Requested/rejected/voided returns carry applied_to_due = to_balance = 0.
-- ---------------------------------------------------------------------------
INSERT INTO purchase_returns (id, return_no, purchase_id, supplier_id, branch_id, status, reason_code, reason, method, subtotal, tax, credit_total, applied_to_due, to_balance, requested_by, approved_by, approved_at, received_by, received_at, credited_by, credited_at, admin_note, created_at) VALUES
(1001,'SEED-PRET-1001',1001,1001,1,'credited','damaged',NULL,'credit_note',480.00,48.00,528.00,0.00,528.00,1,1,'2026-08-01 10:00:00',1,'2026-08-02 11:10:00',1,'2026-08-02 11:20:00',NULL,'2026-08-01 10:00:00'),
(1002,'SEED-PRET-1002',1002,1002,1,'credited','damaged',NULL,'credit_note',360.00,36.00,396.00,0.00,396.00,1002,1,'2026-08-15 09:30:00',1,'2026-08-16 10:00:00',1,'2026-08-16 10:15:00',NULL,'2026-08-15 09:30:00'),
(1003,'SEED-PRET-1003',1004,1001,1,'credited','defective',NULL,'credit_note',925.00,0.00,925.00,0.00,925.00,1002,1,'2026-08-19 11:00:00',1,'2026-08-20 12:10:00',1,'2026-08-20 12:20:00',NULL,'2026-08-19 11:00:00'),
(1004,'SEED-PRET-1004',1006,1008,1,'credited','damaged',NULL,'credit_note',768.00,76.80,844.80,0.00,844.80,1,1,'2026-08-21 09:00:00',1,'2026-08-22 10:00:00',1,'2026-08-22 10:10:00',NULL,'2026-08-21 09:00:00'),
(1005,'SEED-PRET-1005',1008,1007,2,'credited','defective',NULL,'credit_note',660.00,66.00,726.00,0.00,726.00,1,1,'2026-08-24 08:00:00',1,'2026-08-25 09:00:00',1,'2026-08-25 09:10:00',NULL,'2026-08-24 08:00:00'),
(1006,'SEED-PRET-1006',1008,1007,2,'credited','not_as_described',NULL,'credit_note',840.00,84.00,924.00,0.00,924.00,1,1,'2026-08-24 08:30:00',1,'2026-08-25 09:30:00',1,'2026-08-25 09:40:00',NULL,'2026-08-24 08:30:00'),
(1007,'SEED-PRET-1007',1010,1005,1,'credited','defective',NULL,'bank_transfer',17200.00,1720.00,18920.00,0.00,18920.00,1,1,'2026-09-10 09:00:00',1,'2026-09-10 10:00:00',1,'2026-09-10 10:05:00',NULL,'2026-09-10 09:00:00'),
(1008,'SEED-PRET-1008',1011,1010,1,'credited','damaged',NULL,'cash',6450.00,645.00,7095.00,0.00,7095.00,1,1,'2026-09-11 11:00:00',1,'2026-09-12 12:00:00',1,'2026-09-12 12:10:00',NULL,'2026-09-11 11:00:00'),
(1009,'SEED-PRET-1009',1011,1010,1,'credited','wrong_stock',NULL,'credit_note',4320.00,432.00,4752.00,0.00,4752.00,1,1,'2026-09-11 11:30:00',1,'2026-09-12 12:30:00',1,'2026-09-12 12:40:00',NULL,'2026-09-11 11:30:00'),
(1010,'SEED-PRET-1010',1013,1011,3,'credited','damaged',NULL,'credit_note',1520.00,152.00,1672.00,0.00,1672.00,1,1,'2026-09-20 09:00:00',1,'2026-09-20 09:30:00',1,'2026-09-20 09:35:00',NULL,'2026-09-20 09:00:00'),
(1011,'SEED-PRET-1011',1014,1012,1,'credited','not_as_described',NULL,'credit_note',740.00,74.00,814.00,814.00,0.00,1,1,'2026-09-01 09:00:00',1,'2026-09-01 10:00:00',1,'2026-09-01 10:10:00',NULL,'2026-09-01 09:00:00'),
(1012,'SEED-PRET-1012',1003,1004,1,'credited','defective',NULL,'credit_note',4752.00,475.20,5227.20,4752.00,475.20,1002,1,'2026-09-05 10:00:00',1,'2026-09-05 11:00:00',1,'2026-09-05 11:10:00',NULL,'2026-09-05 10:00:00'),
(1013,'SEED-PRET-1013',1013,1011,3,'requested','overstock',NULL,'credit_note',2280.00,228.00,2508.00,0.00,0.00,1003,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-20 14:00:00'),
(1014,'SEED-PRET-1014',1002,1002,1,'rejected','damaged',NULL,'credit_note',90.00,9.00,99.00,0.00,0.00,1002,1,'2026-08-17 09:00:00',NULL,NULL,NULL,NULL,'Supplier refuses the claim - goods were damaged in store storage, not in transit.','2026-08-16 14:00:00'),
(1015,'SEED-PRET-1015',1002,1002,1,'received','damaged',NULL,'credit_note',36.00,3.60,39.60,0.00,0.00,1002,1,'2026-09-01 14:00:00',1,'2026-09-01 15:00:00',NULL,NULL,NULL,'2026-09-01 14:00:00'),
(1016,'SEED-PRET-1016',1006,1008,1,'received','not_as_described',NULL,'credit_note',1920.00,192.00,2112.00,0.00,0.00,1,1,'2026-09-08 13:00:00',1,'2026-09-08 14:00:00',NULL,NULL,NULL,'2026-09-08 13:00:00'),
(1017,'SEED-PRET-1017',1006,1008,1,'cancelled','damaged',NULL,'credit_note',640.00,64.00,704.00,0.00,0.00,1,1,'2026-09-24 11:00:00',1,'2026-09-24 11:30:00',NULL,NULL,'VOIDED: shipped back in error, supplier refused the credit; stock restored and the line tally freed.','2026-09-24 10:00:00'),
(1018,'SEED-PRET-1018',1013,1011,3,'credited','defective',NULL,'credit_note',1520.00,152.00,1672.00,0.00,1672.00,1,1,'2026-09-21 10:00:00',1,'2026-09-21 10:30:00',1,'2026-09-21 10:35:00',NULL,'2026-09-21 10:00:00'),
(1019,'SEED-PRET-1019',1004,1001,1,'credited','damaged',NULL,'bank_transfer',555.00,0.00,555.00,0.00,555.00,1,1,'2026-09-25 10:00:00',1,'2026-09-25 11:00:00',1,'2026-09-25 11:05:00',NULL,'2026-09-25 10:00:00'),
(1020,'SEED-PRET-1020',1001,1001,1,'credited','defective',NULL,'credit_note',4320.00,432.00,4752.00,0.00,4752.00,1,1,'2026-09-27 11:00:00',1,'2026-09-27 12:00:00',1,'2026-09-27 12:05:00',NULL,'2026-09-27 11:00:00');

-- ---------------------------------------------------------------------------
-- 39. purchase_return_items  (20 rows - ids 1001-1020, one line per return)
--  unit_cost comes from the source purchase_items line (client costs are never
--  trusted); qty is positive and total = qty * unit_cost.
-- ---------------------------------------------------------------------------
INSERT INTO purchase_return_items (id, return_id, purchase_item_id, product_id, qty, unit_cost, total, condition_note) VALUES
(1001,1001,1001,1001,10,48.00,480.00,'Bottles intact, seals unbroken'),
(1002,1002,1002,1006,20,18.00,360.00,'Cases unopened'),
(1003,1003,1004,1003,5,185.00,925.00,'Cans dented'),
(1004,1004,1007,1009,6,128.00,768.00,'Boxes water damaged'),
(1005,1005,1009,1013,4,165.00,660.00,'Bars cracked'),
(1006,1006,1010,1010,6,140.00,840.00,'Labels torn'),
(1007,1007,1012,1014,1,17200.00,17200.00,'DOA - screen dead on arrival'),
(1008,1008,1013,1015,3,2150.00,6450.00,'Left earpad detached'),
(1009,1009,1014,1016,4,1080.00,4320.00,'Wrong model received'),
(1010,1010,1016,1008,2,760.00,1520.00,'Powder bags burst'),
(1011,1011,1017,1003,4,185.00,740.00,'Bags torn in transit'),
(1012,1012,1003,1005,66,72.00,4752.00,'Bottles leaking'),
(1013,1013,1016,1008,3,760.00,2280.00,'Overstock - slow mover'),
(1014,1014,1002,1006,5,18.00,90.00,'Bruised packets'),
(1015,1015,1002,1006,2,18.00,36.00,'Jars chipped'),
(1016,1016,1007,1009,15,128.00,1920.00,'Wrong variant delivered'),
(1017,1017,1007,1009,5,128.00,640.00,'Disputed water damage'),
(1018,1018,1016,1008,2,760.00,1520.00,'Seal broken at warehouse'),
(1019,1019,1004,1003,3,185.00,555.00,'Cans expired'),
(1020,1020,1001,1001,90,48.00,4320.00,'Production batch recalled');

-- ---------------------------------------------------------------------------
-- 40. purchase_return_payments  (15 rows - ids 1001-1015; only CREDITED returns
--  pay, mirroring how only REFUNDED sale returns pay. One row per credited
--  return and the amount equals credit_total (the ledger for checks 13d/18).
--  Methods mix credit_note with a couple of cash/bank-transfer recoveries.
--  added_by is the staff user (1 = seed admin) who recorded the credit.
-- ---------------------------------------------------------------------------
INSERT INTO purchase_return_payments (id, return_id, amount, method, reference, notes, added_by, created_at) VALUES
(1001,1001,528.00,'credit_note','CN-1001','Credit note for SEED-PRET-1001.',1,'2026-08-02 11:20:00'),
(1002,1002,396.00,'credit_note','CN-1002','Credit note for SEED-PRET-1002.',1,'2026-08-16 10:15:00'),
(1003,1003,925.00,'credit_note','CN-1003','Credit note for SEED-PRET-1003.',1,'2026-08-20 12:20:00'),
(1004,1004,844.80,'credit_note','CN-1004','Credit note for SEED-PRET-1004.',1,'2026-08-22 10:10:00'),
(1005,1005,726.00,'credit_note','CN-1005','Credit note for SEED-PRET-1005.',1,'2026-08-25 09:10:00'),
(1006,1006,924.00,'credit_note','CN-1006','Credit note for SEED-PRET-1006.',1,'2026-08-25 09:40:00'),
(1007,1007,18920.00,'bank_transfer','TRF-RET-1007','Wire recovery for the returned smartphone (SEED-PRET-1007).',1,'2026-09-10 10:05:00'),
(1008,1008,7095.00,'cash','CASH-RET-1008','Cash recovery for SEED-PRET-1008.',1,'2026-09-12 12:10:00'),
(1009,1009,4752.00,'credit_note','CN-1009','Credit note for SEED-PRET-1009.',1,'2026-09-12 12:40:00'),
(1010,1010,1672.00,'credit_note','CN-1010','Credit note for SEED-PRET-1010.',1,'2026-09-20 09:35:00'),
(1011,1011,814.00,'credit_note','CN-1011','Fully absorbed by PO 1014 due (SEED-PRET-1011).',1,'2026-09-01 10:10:00'),
(1012,1012,5227.20,'credit_note','CN-1012','Split credit - 4752.00 to PO 1003 due, 475.20 to balance (SEED-PRET-1012).',1,'2026-09-05 11:10:00'),
(1013,1018,1672.00,'credit_note','CN-1018','Credit note for SEED-PRET-1018.',1,'2026-09-21 10:35:00'),
(1014,1019,555.00,'bank_transfer','TRF-RET-1019','Overpaid PO 1004 - balance credit for SEED-PRET-1019.',1,'2026-09-25 11:05:00'),
(1015,1020,4752.00,'credit_note','CN-1020','Ceiling-clamp credit on PO 1001 for SEED-PRET-1020.',1,'2026-09-27 12:05:00');

-- ---------------------------------------------------------------------------
-- 38b. return_photos  (3 extra rows - ids 1021-1023, evidence for return 1018,
--  the Phase-14 photo case. Outside the 1001-1020 count window on purpose.)
-- ---------------------------------------------------------------------------
INSERT INTO return_photos (id, return_kind, return_id, return_item_id, path, uploaded_by, created_at) VALUES
(1021,'purchase',1018,1018,'uploads/returns/seed-1018-1.jpg',1,'2026-09-21 10:10:00'),
(1022,'purchase',1018,1018,'uploads/returns/seed-1018-2.jpg',1,'2026-09-21 10:11:00'),
(1023,'purchase',1018,1018,'uploads/returns/seed-1018-3.jpg',1,'2026-09-21 10:12:00');

-- ============================================================================
-- VERIFICATION (run these after the import)
-- ============================================================================
-- 1. Every table should have gained exactly 20 rows (ids 1001-1020). The four
--    Phase-13 tables are in the same union; note their INTENDED deviations:
--    sale_return_payments reports 12 (only refunded returns pay), and
--    sale_return_items reports 20 here because ids 1021/1022 (extra lines of
--    return 1011) intentionally live outside the window. The three Phase-14
--    tables report intended deviations too:
--    purchase_returns          reports 20 (ids 1001-1020)
--    purchase_return_items     reports 20 (ids 1001-1020, one line per return)
--    purchase_return_payments  reports 15 (ids 1001-1015, only credited returns
--                              pay). The Phase-14 stock_logs rows (1024-1041)
--                              deliberately live OUTSIDE the window below, so
--                              stock_logs still reports 20:
SELECT 'activity_logs' t, COUNT(*) c FROM activity_logs WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'banners', COUNT(*) FROM banners WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'branches', COUNT(*) FROM branches WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'brands', COUNT(*) FROM brands WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'cart', COUNT(*) FROM cart WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'cart_items', COUNT(*) FROM cart_items WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'categories', COUNT(*) FROM categories WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'coupons', COUNT(*) FROM coupons WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'customers', COUNT(*) FROM customers WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'inventory', COUNT(*) FROM inventory WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'invoices', COUNT(*) FROM invoices WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'notifications', COUNT(*) FROM notifications WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'order_items', COUNT(*) FROM order_items WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'orders', COUNT(*) FROM orders WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'permissions', COUNT(*) FROM permissions WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'product_gallery', COUNT(*) FROM product_gallery WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'product_reviews', COUNT(*) FROM product_reviews WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'products', COUNT(*) FROM products WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'purchase_items', COUNT(*) FROM purchase_items WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'purchase_payments', COUNT(*) FROM purchase_payments WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'purchase_return_items', COUNT(*) FROM purchase_return_items WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'purchase_return_payments', COUNT(*) FROM purchase_return_payments WHERE id BETWEEN 1001 AND 1015
UNION ALL SELECT 'purchase_returns', COUNT(*) FROM purchase_returns WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'purchases', COUNT(*) FROM purchases WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'return_photos', COUNT(*) FROM return_photos WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'roles', COUNT(*) FROM roles WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'sale_items', COUNT(*) FROM sale_items WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'sale_payments', COUNT(*) FROM sale_payments WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'sale_return_items', COUNT(*) FROM sale_return_items WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'sale_return_payments', COUNT(*) FROM sale_return_payments WHERE id BETWEEN 1001 AND 1012
UNION ALL SELECT 'sale_returns', COUNT(*) FROM sale_returns WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'sales', COUNT(*) FROM sales WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'settings', COUNT(*) FROM settings WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'stock_logs', COUNT(*) FROM stock_logs WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'stores', COUNT(*) FROM stores WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'suppliers', COUNT(*) FROM suppliers WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'units', COUNT(*) FROM units WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'user_sessions', COUNT(*) FROM user_sessions WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'users', COUNT(*) FROM users WHERE id BETWEEN 1001 AND 1020
UNION ALL SELECT 'wishlist', COUNT(*) FROM wishlist WHERE id BETWEEN 1001 AND 1020;

-- 2. products.total_stock must equal SUM(inventory.qty) - expect 0 mismatches:
SELECT p.id, p.sku, p.total_stock, p.branch_count,
       COALESCE(SUM(i.qty),0) inv_qty, COUNT(i.id) inv_rows
FROM products p
LEFT JOIN inventory i ON i.product_id = p.id
WHERE p.id BETWEEN 1001 AND 1020
GROUP BY p.id, p.sku, p.total_stock, p.branch_count
HAVING p.total_stock <> COALESCE(SUM(i.qty),0) OR p.branch_count <> COUNT(i.id);

-- 3. purchase_payments must sum to purchases.paid - expect 0 mismatches:
SELECT p.id, p.po_number, p.paid, COALESCE(SUM(pp.amount),0) paid_sum
FROM purchases p
LEFT JOIN purchase_payments pp ON pp.purchase_id = p.id
WHERE p.id BETWEEN 1001 AND 1020
GROUP BY p.id, p.po_number, p.paid
HAVING p.paid <> COALESCE(SUM(pp.amount),0);

-- 4. sale_payments must sum to sales.grand_total - expect 0 mismatches.
--    Sales 1004 (returned) and 1008 (held) are EXCLUDED on purpose: a held
--    sale is never paid, and the returned sale was refunded via the customer
--    account rather than a sale_payments row, so neither carries a payment.
SELECT s.id, s.invoice_no, s.grand_total, COALESCE(SUM(sp.amount),0) paid_sum
FROM sales s
LEFT JOIN sale_payments sp ON sp.sale_id = s.id
WHERE s.id BETWEEN 1001 AND 1020 AND s.id NOT IN (1004,1008)
GROUP BY s.id, s.invoice_no, s.grand_total
HAVING s.grand_total <> COALESCE(SUM(sp.amount),0);

-- 5. grand_total must equal subtotal - discount + tax + shipping - expect 0 rows:
SELECT id, invoice_no, subtotal, discount, tax, shipping, grand_total
FROM sales
WHERE id BETWEEN 1001 AND 1020
AND grand_total <> (subtotal - discount + tax + shipping);

-- 6a. orders.subtotal must be the GROSS of order_items.total, and
--     grand_total = subtotal - discount + tax + shipping - expect 0 rows:
SELECT o.id, o.order_no, o.subtotal, o.discount, SUM(oi.total) items_total
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
WHERE o.id BETWEEN 1001 AND 1020
GROUP BY o.id, o.order_no, o.subtotal, o.discount
HAVING o.subtotal <> SUM(oi.total);

-- 6b. sales.subtotal must equal SUM(sale_items.total) - expect 0 rows:
SELECT s.id, s.invoice_no, s.subtotal, SUM(si.total) items_total
FROM sales s
JOIN sale_items si ON si.sale_id = s.id
WHERE s.id BETWEEN 1001 AND 1020
GROUP BY s.id, s.invoice_no, s.subtotal
HAVING s.subtotal <> SUM(si.total);

-- 5b. purchases.subtotal must equal SUM(purchase_items.total), and a purchase
--      with a non-zero subtotal must actually have lines - expect 0 rows:
SELECT p.id, p.po_number, p.subtotal, COALESCE(SUM(pi.total),0) items_total,
       COUNT(pi.id) item_rows
FROM purchases p
LEFT JOIN purchase_items pi ON pi.purchase_id = p.id
WHERE p.id BETWEEN 1001 AND 1020
GROUP BY p.id, p.po_number, p.subtotal
HAVING p.subtotal <> COALESCE(SUM(pi.total),0)
    OR (p.subtotal > 0 AND COUNT(pi.id) = 0);

-- 5c. purchases.total / due arithmetic - expect 0 rows:
SELECT id, po_number, subtotal, tax, total, paid, due
FROM purchases
WHERE id BETWEEN 1001 AND 1020
  AND (total <> (subtotal + tax) OR due <> (total - paid));

-- 5d. purchase_items.total must equal qty * unit_cost - expect 0 rows:
SELECT id, purchase_id, qty, unit_cost, total
FROM purchase_items
WHERE id BETWEEN 1001 AND 1020 AND total <> (qty * unit_cost);

-- 6. order grand_total arithmetic - expect 0 rows:
SELECT id, order_no, subtotal, discount, tax, shipping, grand_total
FROM orders
WHERE id BETWEEN 1001 AND 1020
AND grand_total <> (subtotal - discount + tax + shipping);

-- 7. FK integrity across the seeded range - expect no rows:
SELECT 'purchase_items' t, pi.id FROM purchase_items pi LEFT JOIN purchases p ON p.id=pi.purchase_id LEFT JOIN products pr ON pr.id=pi.product_id WHERE pi.id BETWEEN 1001 AND 1020 AND (p.id IS NULL OR pr.id IS NULL)
UNION ALL SELECT 'purchase_payments', x.id FROM (SELECT id,purchase_id FROM purchase_payments WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN purchases p ON p.id=x.purchase_id WHERE p.id IS NULL
UNION ALL SELECT 'sale_items', x.id FROM (SELECT id,sale_id,product_id FROM sale_items WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN sales s ON s.id=x.sale_id LEFT JOIN products pr ON pr.id=x.product_id WHERE s.id IS NULL OR pr.id IS NULL
UNION ALL SELECT 'inventory', x.id FROM (SELECT id,product_id,branch_id FROM inventory WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN products p ON p.id=x.product_id LEFT JOIN branches b ON b.id=x.branch_id WHERE p.id IS NULL OR b.id IS NULL
UNION ALL SELECT 'cart_items', x.id FROM (SELECT id,cart_id,product_id FROM cart_items WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN cart c ON c.id=x.cart_id LEFT JOIN products p ON p.id=x.product_id WHERE c.id IS NULL OR p.id IS NULL
UNION ALL SELECT 'order_items', x.id FROM (SELECT id,order_id,product_id FROM order_items WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN orders o ON o.id=x.order_id LEFT JOIN products p ON p.id=x.product_id WHERE o.id IS NULL OR p.id IS NULL
UNION ALL SELECT 'wishlist', x.id FROM (SELECT id,user_id,product_id FROM wishlist WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN users u ON u.id=x.user_id LEFT JOIN products p ON p.id=x.product_id WHERE u.id IS NULL OR p.id IS NULL
UNION ALL SELECT 'product_reviews', x.id FROM (SELECT id,product_id,user_id FROM product_reviews WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN products p ON p.id=x.product_id LEFT JOIN users u ON u.id=x.user_id WHERE p.id IS NULL OR u.id IS NULL
UNION ALL SELECT 'invoices', x.id FROM (SELECT id,sale_id FROM invoices WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN sales s ON s.id=x.sale_id WHERE s.id IS NULL;

-- 8. stock_logs must reference a document that really contains the product.
--    A 'purchase' log must match a purchase_items row, a 'sale' log must match
--    a sale_items row, and a 'sale_return' log must resolve through the return
--    header (reference_id = RETURN id, see Inventory::recordReturn) to a
--    sale line carrying the same product. Expect 0 rows:
SELECT sl.id, sl.type, sl.product_id log_product, sl.qty log_qty,
       sl.reference_type, sl.reference_id,
       COALESCE(pi.product_id, si.product_id, rsi.product_id) ref_product
FROM stock_logs sl
LEFT JOIN purchase_items pi
       ON sl.reference_type = 'purchase' AND pi.purchase_id = sl.reference_id
      AND pi.product_id = sl.product_id
LEFT JOIN sale_items si
       ON sl.reference_type = 'sale' AND si.sale_id = sl.reference_id
      AND si.product_id = sl.product_id
LEFT JOIN sale_returns sr
       ON sl.reference_type = 'sale_return' AND sr.id = sl.reference_id
LEFT JOIN sale_items rsi
       ON sl.reference_type = 'sale_return'
      AND rsi.sale_id = COALESCE(sr.sale_id, 0)
      AND rsi.product_id = sl.product_id
WHERE sl.id BETWEEN 1001 AND 1023
  AND (   (sl.reference_type = 'purchase'     AND pi.id IS NULL)
       OR (sl.reference_type = 'sale'         AND si.id IS NULL)
       OR (sl.reference_type = 'sale_return'
           AND (sr.id IS NULL OR rsi.id IS NULL)) );

-- 9. no stock movement may exist for the HELD sale (1008) or the CANCELLED
--    sale (1019) - a held sale is never paid or shipped, a cancelled sale is
--    rolled back, and a return can never exist against either. sale_return
--    logs resolve through the return header to its sale. Expect 0 rows:
SELECT sl.id, sl.product_id, sl.type, sl.qty, sl.reference_type, sl.reference_id
FROM stock_logs sl
LEFT JOIN sale_returns sr
       ON sl.reference_type = 'sale_return' AND sr.id = sl.reference_id
WHERE sl.id BETWEEN 1001 AND 1023
  AND (   (sl.reference_type IN ('sale') AND sl.reference_id IN
            (SELECT id FROM sales WHERE id BETWEEN 1001 AND 1020
              AND status IN ('held','cancelled')))
       OR (sl.reference_type = 'sale_return' AND COALESCE(sr.sale_id, 0) IN
            (SELECT id FROM sales WHERE id BETWEEN 1001 AND 1020
              AND status IN ('held','cancelled'))) );

-- 10. activity_logs.resource_id must point at a row that actually exists for
--     user-facing resources. Expect 0 rows:
SELECT al.id, al.action, al.resource, al.resource_id
FROM activity_logs al
WHERE al.id BETWEEN 1001 AND 1020
  AND (   (al.resource = 'users'           AND NOT EXISTS (SELECT 1 FROM users u WHERE u.id = al.resource_id))
       OR (al.resource = 'sales'           AND NOT EXISTS (SELECT 1 FROM sales s WHERE s.id = al.resource_id))
       OR (al.resource = 'purchases'       AND NOT EXISTS (SELECT 1 FROM purchases p WHERE p.id = al.resource_id))
       OR (al.resource = 'orders'          AND NOT EXISTS (SELECT 1 FROM orders o WHERE o.id = al.resource_id))
       OR (al.resource = 'products'        AND NOT EXISTS (SELECT 1 FROM products p WHERE p.id = al.resource_id))
       OR (al.resource = 'inventory'       AND NOT EXISTS (SELECT 1 FROM inventory i WHERE i.id = al.resource_id))
       OR (al.resource = 'product_reviews' AND NOT EXISTS (SELECT 1 FROM product_reviews r WHERE r.id = al.resource_id)) );

-- 11. POS-linked orders must actually mirror their sale: same customer, same
--     grand_total, and a grand_total that matches. Expect 0 rows:
SELECT o.id, o.order_no, o.sale_id, o.customer_id order_customer,
       s.customer_id sale_customer, o.grand_total order_total, s.grand_total sale_total
FROM orders o
JOIN sales s ON s.id = o.sale_id
WHERE o.id BETWEEN 1001 AND 1020
  AND (o.customer_id <> s.customer_id OR o.grand_total <> s.grand_total);

-- 12. orders.sale_id must reference a seeded sale - expect 0 rows:
SELECT o.id, o.order_no, o.sale_id
FROM orders o
LEFT JOIN sales s ON s.id = o.sale_id
WHERE o.id BETWEEN 1001 AND 1020 AND o.sale_id IS NOT NULL AND s.id IS NULL;

-- 13. FK integrity across the four Phase-13 tables - expect no rows:
SELECT 'sale_return_items.product' t, x.id FROM (SELECT id, product_id FROM sale_return_items WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN products p ON p.id=x.product_id WHERE p.id IS NULL
UNION ALL SELECT 'sale_return_items.sale_item', x.id FROM (SELECT id, sale_item_id FROM sale_return_items WHERE id BETWEEN 1001 AND 1020 AND sale_item_id IS NOT NULL) x LEFT JOIN sale_items si ON si.id=x.sale_item_id WHERE si.id IS NULL
UNION ALL SELECT 'sale_return_items.order_item', x.id FROM (SELECT id, order_item_id FROM sale_return_items WHERE id BETWEEN 1001 AND 1020 AND order_item_id IS NOT NULL) x LEFT JOIN order_items oi ON oi.id=x.order_item_id WHERE oi.id IS NULL
UNION ALL SELECT 'sale_return_payments.return', x.id FROM (SELECT id, return_id FROM sale_return_payments WHERE id BETWEEN 1001 AND 1012) x LEFT JOIN sale_returns r ON r.id=x.return_id WHERE r.id IS NULL
UNION ALL SELECT 'sale_return_payments.user', x.id FROM (SELECT id, processed_by FROM sale_return_payments WHERE id BETWEEN 1001 AND 1012) x LEFT JOIN users u ON u.id=x.processed_by WHERE u.id IS NULL
UNION ALL SELECT 'return_photos.return', x.id FROM (SELECT id, return_id FROM return_photos WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN sale_returns r ON r.id=x.return_id WHERE r.id IS NULL
UNION ALL SELECT 'return_photos.item', x.id FROM (SELECT id, return_item_id FROM return_photos WHERE id BETWEEN 1001 AND 1020 AND return_item_id IS NOT NULL) x LEFT JOIN sale_return_items sri ON sri.id=x.return_item_id WHERE sri.id IS NULL
UNION ALL SELECT 'return_photos.user', x.id FROM (SELECT id, uploaded_by FROM return_photos WHERE id BETWEEN 1001 AND 1020) x LEFT JOIN users u ON u.id=x.uploaded_by WHERE u.id IS NULL;

-- 13b. a return line must never claim more units than its sale/order line sold
--      (checked via the source line), and its money must be qty * unit_price:
SELECT sri.id, sri.qty, sri.unit_price, sri.discount, sri.total
FROM sale_return_items sri
WHERE sri.id BETWEEN 1001 AND 1020
  AND (   sri.total <> (sri.qty * sri.unit_price)
       OR sri.qty <= 0
       OR sri.qty > COALESCE(
            (SELECT si.qty FROM sale_items si WHERE si.id = sri.sale_item_id),
            (SELECT oi.qty FROM order_items oi WHERE oi.id = sri.order_item_id),
            0) );

-- 13c. return.subtotal must equal SUM(sale_return_items.total), and a return
--      must actually have lines - expect 0 rows:
SELECT r.id, r.return_no, r.subtotal, COALESCE(SUM(sri.total),0) items_total,
       COUNT(sri.id) line_count
FROM sale_returns r
LEFT JOIN sale_return_items sri ON sri.return_id = r.id
WHERE r.id BETWEEN 1001 AND 1020
GROUP BY r.id, r.return_no, r.subtotal
HAVING r.subtotal <> COALESCE(SUM(sri.total),0)
    OR (r.subtotal > 0 AND COUNT(sri.id) = 0);

-- 13d. refund money only ever leaves the till for REFUNDED returns, and then
--      it must be the full computed refund_total - expect 0 rows:
SELECT srp.id, srp.return_id, srp.amount, sr.status, sr.refund_total
FROM sale_return_payments srp
JOIN sale_returns sr ON sr.id = srp.return_id
WHERE srp.id BETWEEN 1001 AND 1012
  AND (sr.status <> 'refunded' OR srp.amount <> sr.refund_total);

-- 14. refund_total must equal the reversed shares exactly:
--     subtotal - discount + tax + shipping - expect 0 rows:
SELECT id, return_no, subtotal, discount, tax, shipping, refund_total
FROM sale_returns
WHERE id BETWEEN 1001 AND 1020
  AND refund_total <> (subtotal - discount + tax + shipping);

-- 15. a refund must never exceed what its sale/order actually took in, and the
--     SUM of partial refunds (refunded + received) on one sale must not exceed
--     grand_total either. Sale 1020 hits the exact ceiling: 476.67 + 238.33 =
--     715.00. Expect 0 rows:
SELECT sr.id, sr.return_no, sr.refund_total, sr.status,
       COALESCE(s.grand_total, o.grand_total) source_total
FROM sale_returns sr
LEFT JOIN sales s ON s.id = sr.sale_id
LEFT JOIN orders o ON o.id = sr.order_id
WHERE sr.id BETWEEN 1001 AND 1020
  AND sr.refund_total > COALESCE(s.grand_total, o.grand_total);

-- 15b. partial refunds must never exceed the sale total - expect 0 rows:
SELECT x.id, x.invoice_no, x.grand_total, x.refund_sum
FROM (
  SELECT s.id, s.invoice_no, s.grand_total, COALESCE(SUM(sr.refund_total),0) refund_sum
  FROM sales s
  LEFT JOIN sale_returns sr ON sr.sale_id = s.id
    AND sr.status IN ('refunded','received')
  WHERE s.id BETWEEN 1001 AND 1020
  GROUP BY s.id, s.invoice_no, s.grand_total
) x
WHERE x.refund_sum > x.grand_total;

-- 16. restock ledger agrees with the receive() decisions. Aggregated per
--     (return, product): SUM of sellable units (restock=1) must equal what
--     stock_logs actually put back, and nothing may come back for units that
--     were not sellable. Aggregation (not a per-line join) is required because
--     return 1011 has three identical product lines but only two restock logs.
--     Expect 0 rows:
SELECT x.return_id, x.product_id, x.expected_restock,
       COALESCE(x.logged_restock, 0) logged_restock
FROM (
  SELECT sri.return_id, sri.product_id,
         SUM(CASE WHEN sri.restock = 1 THEN sri.qty ELSE 0 END) expected_restock,
         (SELECT COALESCE(SUM(sl.qty),0) FROM stock_logs sl
           JOIN sale_returns sr ON sr.id = sl.reference_id
          WHERE sl.reference_type = 'sale_return' AND sl.id BETWEEN 1001 AND 1023
            AND sr.id = sri.return_id AND sl.product_id = sri.product_id) logged_restock
  FROM sale_return_items sri
  WHERE sri.return_id BETWEEN 1001 AND 1020
  GROUP BY sri.return_id, sri.product_id
) x
WHERE x.expected_restock <> x.logged_restock;

-- 16b. every sale_return stock log must pair with a restockable (restock=1)
--      item on the same return - no side-channel restocks. Expect 0 rows:
SELECT sl.id, sl.reference_id, sl.product_id, sl.qty
FROM stock_logs sl
LEFT JOIN sale_return_items sri
       ON sri.return_id = sl.reference_id
      AND sri.product_id = sl.product_id
      AND sri.restock = 1
WHERE sl.reference_type = 'sale_return' AND sl.id BETWEEN 1001 AND 1023
  AND sri.id IS NULL;

-- ============================================================================
-- PHASE 14 checks (accounts, stock and money follow supplier returns exactly)
-- ============================================================================

-- 17. Every supplier-return line is positive and belongs to a live return
--     (the doc's check). Expect 0 violations:
SELECT '17. no negative or orphaned supplier return lines' AS check_name,
       COUNT(*) AS violations FROM purchase_return_items ri
LEFT JOIN purchase_returns r ON r.id = ri.return_id
WHERE r.id IS NULL OR ri.qty <= 0;

-- 18. Credit arithmetic: credit_total = subtotal + tax (the doc's check).
--     Applies to EVERY row including requested/rejected/voided ones, so the
--     money shown on those documents stays internally consistent. Expect 0:
SELECT '18. supplier credit arithmetic' AS check_name, COUNT(*) AS violations
FROM purchase_returns
WHERE credit_total <> ROUND(subtotal + tax, 2);

-- 19. No supplier runs a negative balance (the doc's check). Expect 0:
SELECT '19. supplier balance never negative' AS check_name, COUNT(*) AS violations
FROM suppliers WHERE balance < 0;

-- 19b. a supplier-return line's money is qty * unit_cost where unit_cost comes
--      straight from the source purchase_items row (client costs are never
--      trusted), and the aggregated non-cancelled return tally cannot exceed
--      the PO's received_qty (the over-return guard in
--      PurchaseReturn::getReturnableForPurchase). The PO 1003 / item 1003 line
--      (legacy received_qty = 0) is the documented split-credit exception, and
--      lines on POs that were never received are skipped. Expect 0 rows:
SELECT pi.id, pi.received_qty, COALESCE(SUM(ri.qty),0) return_tally
FROM purchase_items pi
LEFT JOIN purchase_return_items ri ON ri.purchase_item_id = pi.id
LEFT JOIN purchase_returns r ON r.id = ri.return_id
WHERE pi.id BETWEEN 1001 AND 1020
  AND pi.received_qty > 0 AND pi.id <> 1003
  AND r.status <> 'cancelled'
GROUP BY pi.id, pi.received_qty
HAVING COALESCE(SUM(ri.qty),0) > pi.received_qty;

-- 19c. only CREDITED returns pay, and then the payment equals the full
--      credit_total (mirrors 13d on the sale side). Expect 0 rows:
SELECT prp.id, prp.return_id, prp.amount, r.status, r.credit_total
FROM purchase_return_payments prp
JOIN purchase_returns r ON r.id = prp.return_id
WHERE prp.id BETWEEN 1001 AND 1015
  AND (r.status <> 'credited' OR prp.amount <> r.credit_total);

-- 19d. return.subtotal must equal SUM(purchase_return_items.total), and a
--      return must actually have lines. Expect 0 rows:
SELECT r.id, r.return_no, r.subtotal, COALESCE(SUM(ri.total),0) items_total,
       COUNT(ri.id) line_count
FROM purchase_returns r
LEFT JOIN purchase_return_items ri ON ri.return_id = r.id
WHERE r.id BETWEEN 1001 AND 1020
GROUP BY r.id, r.return_no, r.subtotal
HAVING r.subtotal <> COALESCE(SUM(ri.total),0)
    OR (r.subtotal > 0 AND COUNT(ri.id) = 0);

-- 19e. every Phase-14 stock log (1024-1041) must resolve to a live return
--      carrying the same product: the 'purchase_return' out rows ship goods
--      back and the 'purchase_return_void' restore row pairs with the voided
--      return 1017. Expect 0 rows:
SELECT sl.id, sl.type, sl.qty, sl.reference_type, sl.reference_id
FROM stock_logs sl
LEFT JOIN purchase_return_items ri
       ON ri.return_id = sl.reference_id AND ri.product_id = sl.product_id
WHERE sl.id BETWEEN 1024 AND 1041 AND ri.id IS NULL;

-- 19f. purchase_items.returned_qty must equal the tally of shipped-back
--      (received + credited) return lines; requested/rejected counts nothing
--      and VOIDED (cancelled) returns free the tally. The status filter lives
--      in a subquery so unmatched returns stay out of the SUM. Expect 0 rows:
SELECT pi.id, pi.returned_qty, COALESCE(SUM(ri.qty),0) shipped_back
FROM purchase_items pi
LEFT JOIN (
    SELECT q.purchase_item_id, q.qty
    FROM purchase_return_items q
    JOIN purchase_returns rv ON rv.id = q.return_id
    WHERE rv.status IN ('received','credited')
) ri ON ri.purchase_item_id = pi.id
WHERE pi.id BETWEEN 1001 AND 1020 AND pi.returned_qty > 0
GROUP BY pi.id, pi.returned_qty
HAVING pi.returned_qty <> COALESCE(SUM(ri.qty),0);

-- 19g. suppliers.balance equals the net of credited-return overflow
--      (SUM of applied crediting that exceeded the PO's due). A supplier that
--      absorbed everything into its PO due keeps balance 0. Expect 0 rows:
SELECT s.id, s.company_name, s.balance seeded_balance,
       COALESCE(SUM(r.to_balance),0) credited_overflow
FROM suppliers s
LEFT JOIN purchase_returns r ON r.supplier_id = s.id AND r.status = 'credited'
WHERE s.id BETWEEN 1001 AND 1020
GROUP BY s.id, s.company_name, s.balance
HAVING s.balance <> COALESCE(SUM(r.to_balance),0);

-- ============================================================================
-- REMOVE SEED DATA  (only for a clean re-seed; deletes ids 1001-1020 only,
--                    the Phase-13 extras 1001-1023/1022, the split lines, and
--                    the Phase-14 extras 1024-1041 / purchase-return photos)
-- ============================================================================
-- DELETE FROM purchase_return_payments WHERE id BETWEEN 1001 AND 1015;
-- DELETE FROM purchase_return_items    WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM purchase_returns         WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM return_photos         WHERE id BETWEEN 1001 AND 1023;
-- DELETE FROM sale_return_payments  WHERE id BETWEEN 1001 AND 1012;
-- DELETE FROM sale_return_items     WHERE id BETWEEN 1001 AND 1022;
-- DELETE FROM sale_returns          WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM order_items   WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM orders        WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM product_reviews WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM wishlist      WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM cart_items    WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM cart          WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM product_gallery WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM banners       WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM settings      WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM activity_logs WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM notifications WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM invoices      WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM stock_logs    WHERE id BETWEEN 1001 AND 1041;
-- DELETE FROM inventory     WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM sale_payments WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM sale_items    WHERE id BETWEEN 1001 AND 1022;
-- DELETE FROM sales         WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM purchase_payments WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM purchase_items    WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM purchases         WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM customers         WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM suppliers         WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM products          WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM units             WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM brands            WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM categories        WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM user_sessions     WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM users             WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM branches          WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM permissions       WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM roles             WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM stores            WHERE id BETWEEN 1001 AND 1020;
-- DELETE FROM coupons           WHERE id BETWEEN 1001 AND 1020;

-- ============================================================================
-- END OF SEED DATA
-- ============================================================================
