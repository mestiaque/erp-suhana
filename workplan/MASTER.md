# সব Master Data (duplicate গুলো একই key বারবার লিখে দেখানো হয়েছে — কতবার আছে বোঝার জন্য)

```php
[
    // ---------- DUPLICATE (একই master data, একাধিক module-এ আলাদা টেবিল) ----------
    'department'     => 'hr',
    'department'     => 'sfl-inventory',
    'department'     => 'production-trace',
    'department'     => 'merchandising-trace',

    'warehouse'      => 'sfl-inventory',        // inv_stores
    'warehouse'      => 'production-trace',     // trc_warehouses
    'warehouse'      => 'production-trace',     // trc_locations

    'supplier'       => 'merchandising-trace',
    'supplier'       => 'sfl-inventory',
    'supplier'       => 'production-trace',     // trc_vendors

    'buyer'          => 'merchandising-trace',
    'buyer'          => 'sfl-inventory',

    'color'          => 'merchandising-trace',
    'color'          => 'sfl-inventory',

    'size'           => 'merchandising-trace',
    'size'           => 'sfl-inventory',

    'item'           => 'merchandising-trace',
    'item'           => 'sfl-inventory',

    'item_category'  => 'merchandising-trace',
    'item_category'  => 'sfl-inventory',

    'uom'            => 'merchandising-trace',
    'uom'            => 'sfl-inventory',        // inv_units

    'season'         => 'merchandising-trace',
    'season'         => 'production-trace',

    'operator'       => 'sfl-inventory',
    'operator'       => 'production-trace',

    'machine'        => 'sfl-inventory',
    'machine'        => 'production-trace',

    'factory'        => 'hr',
    'factory'        => 'merchandising-trace',

    'payment_method' => 'hr',
    'payment_method' => 'acc-sfl',

    'line'           => 'hr',                   // hr_floor_lines
    'line'           => 'production-trace',     // trc_lines

    // ---------- UNIQUE (শুধু একটা module-এই আছে, duplicate না) ----------
    'section'          => 'hr',
    'sub_section'      => 'hr',
    'designation'      => 'hr',
    'shift'            => 'hr',
    'weekday'          => 'hr',
    'holiday'          => 'hr',
    'working_place'    => 'hr',
    'geo_location'     => 'hr',
    'religion'         => 'hr',
    'marital_status'   => 'hr',
    'sex'              => 'hr',
    'classification'   => 'hr',
    'leave_info'       => 'hr',
    'salary_key'       => 'hr',
    'bonus_title'      => 'hr',
    'bonus_policy'     => 'hr',
    'asset_category'   => 'hr',
    'asset_location'   => 'hr',
    'employee'         => 'hr',

    'buyer_contact'    => 'merchandising-trace',
    'ship_mode'        => 'merchandising-trace',
    'wash_type'        => 'merchandising-trace',
    'product_type'     => 'merchandising-trace',
    'currency'         => 'merchandising-trace',
    'sample_type'      => 'merchandising-trace',
    'style_image_type' => 'merchandising-trace',
    'document_template'=> 'merchandising-trace',
    'tna_template'     => 'merchandising-trace',

    'branch'           => 'acc-sfl',
    'account'          => 'acc-sfl',
    'master_particular'=> 'acc-sfl',
    'particular'       => 'acc-sfl',
    'fiscal_year'      => 'acc-sfl',

    'brand'            => 'sfl-inventory',

    'product'          => 'production-trace',   // bridged into merchandising-trace
    'size_group'       => 'production-trace',   // bridged into merchandising-trace
    'fabric'           => 'production-trace',
    'trim'             => 'production-trace',
    'part'             => 'production-trace',   // bridged into merchandising-trace
    'defect_type'      => 'production-trace',
    'workflow_stage'   => 'production-trace',
    'aql_table'        => 'production-trace',
];
```

---

# Master Data Sync Architecture

**নিয়ম:**
- **root** module = mandatory/canonical — create, edit, delete শুধু এখানেই হবে।
- বাকি (non-root) module গুলোতে নিজের local টেবিল থাকবে (নিজের package-এর ভেতরের FK/relation ঠিক রাখার জন্য), কিন্তু create/edit ফর্ম থাকবে না — শুধু index (read-only listing) + **"Sync"** বাটন।
- Sync বাটনে click করলে root টেবিল থেকে ডেটা টেনে local টেবিলে upsert হবে (name/code/is_active ইত্যাদি কপি হবে)।
- Local row root-এর কোন রেকর্ড থেকে সিঙ্ক হয়েছে সেটা মনে রাখার জন্য local টেবিলে polymorphic **morph relation** কলাম থাকবে: `source_type`, `source_id` (Laravel `morphTo`) — কারণ প্রতিটা concept-এর root module আলাদা (কখনো hr, কখনো sfl-inventory, কখনো merchandising-trace), তাই generic morph কলাম দিয়েই সবগুলোর sync একই ট্রেইট/মেকানিজমে handle করা যাবে, আলাদা আলাদা FK লিখতে হবে না।
- `(source_type, source_id)`-এর উপর unique index থাকবে যাতে বারবার sync করলে duplicate row তৈরি না হয়।

```php
[
    'department' => [
        'root' => 'hr',                                                    // create/edit এখানে
        'sync' => ['sfl-inventory', 'production-trace', 'merchandising-trace'], // এখানে শুধু sync button + morph relation
    ],
    'warehouse' => [
        'root' => 'sfl-inventory',
        'sync' => ['production-trace'],
    ],
    'supplier' => [
        'root' => 'sfl-inventory',
        'sync' => ['merchandising-trace', 'production-trace'],
    ],
    'buyer' => [
        'root' => 'merchandising-trace',
        'sync' => ['sfl-inventory'],
    ],
    'color' => [
        'root' => 'merchandising-trace',
        'sync' => ['sfl-inventory'],
    ],
    'size' => [
        'root' => 'merchandising-trace',
        'sync' => ['sfl-inventory'],
    ],
    'item' => [
        'root' => 'sfl-inventory',
        'sync' => ['merchandising-trace'],
    ],
    'item_category' => [
        'root' => 'sfl-inventory',
        'sync' => ['merchandising-trace'],
    ],
    'uom' => [
        'root' => 'sfl-inventory',      // uom এখানে create হবে
        'sync' => ['merchandising-trace'], // বাকিতে শুধু sync button থাকবে
    ],
    'season' => [
        'root' => 'merchandising-trace',
        'sync' => ['production-trace'],
    ],
    'operator' => [
        'root' => 'production-trace',
        'sync' => ['sfl-inventory'],
    ],
    'machine' => [
        'root' => 'production-trace',
        'sync' => ['sfl-inventory'],
    ],
    'factory' => [
        'root' => 'hr',
        'sync' => ['merchandising-trace'],
    ],
    'payment_method' => [
        'root' => 'acc-sfl',
        'sync' => ['hr'],
    ],
    'line' => [
        'root' => 'production-trace',
        'sync' => ['hr'],
    ],
];
```
