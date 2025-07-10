<?php 



// echo __FILE__.\_\BR;
// \_\pre($this); echo \_\BR;
// \_\pre(o('customer')->model[3]); echo \_\BR;
// \_\pre(o('customer')->model->get(2)); echo \_\BR;

namespace _\html { if(!\function_exists(val::class)){ function val(array|string $a, string $k){
    if(\is_array($a)){
        return ($v = $a[$k] ?? null) ? \htmlspecialchars($v) : null;
    } else {
        return \htmlspecialchars($a);
    }
}}}


namespace _\form { if(!\function_exists(val::class)){ function val($key,array &$a){
    if(isset($a[$key])){
        return \htmlspecialchars($a[$key]);
    } else {
        return null;
    }
}}}

namespace _\form { if(!\function_exists(key::class)){ function key($key){
    return "name=\"--form[{$key}]\"";
}}}

namespace _\form { if(!\function_exists(kvp::class)){ function kvp($key, array &$a){
    if(isset($a[$key])){
        return "name=\"--form[{$key}]\" value=\"".\htmlspecialchars($a[$key])."\"";
    } else {
        return "name=\"--form[{$key}]\" value=\"\"";
    }
}}}


namespace { return new class extends \_\xui\pool\alpha\controller {
    
    protected function data__query(){
        $this->vars['browser/title'] = 'Invoicing | Orders';
        $this->vars['table/header'] = 'Orders';
        $this->vars['table/button_new']['href'] = \_\CTLR_URL.'/item_edit';
        $this->vars['table/button_new']['text'] = "+ Create New Order";
        $this->vars['table/query']['from'] = "`order__tbl` as o";
        $this->vars['table/query']['selects'] = "
            o.id,
            o.order_code, 
            o.order_date, 
            o.ex_gst_total, 
            o.grand_total, 
            o.paid,
            o.paid_on,
            v.customer as customer_name, 
            v.rego as vehicle_rego,
            v.email as customer_email,
            v.phone as customer_phone
        ";
        $this->vars['table/query']['joins'] = "
            LEFT JOIN `vehicle__tbl` v ON o.vehicle_id = v.id
        ";
        $this->vars['table/query']['wheres'] = "
            WHERE (v.customer LIKE :search OR v.rego LIKE :search2)
        ";
        $this->vars['table/query']['orders'] = "
            ORDER BY o.order_date DESC
        ";
        $this->vars['table/query']['on__prepare'] = function($stmt){
            $search = $this->vars['table/params']['search'] ?? '';
            $stmt->bindValue(':search', "%{$search}%", \PDO::PARAM_STR);
            $stmt->bindValue(':search2', "%{$search}%", \PDO::PARAM_STR);
        };
        $this->vars['table/result']['on__adjust'] = function(&$rows){
            foreach($rows as &$ox){
                if(!empty($ox['paid_on'])){
                    $ox['paid_on'] = (new \DateTime($ox['paid_on']))->format('Y-m-d');
                }
            }
        };

    }
    
    protected function inset__prt(){
        ?>
        <div class="container-fluid mt-4 table-responsive">
            <!-- Orders Table -->
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" scope="col">#</th>
                        <th class="text-center" scope="col">Order Code</th>
                        <th class="text-center" scope="col">Vehicle</th>
                        <th class="text-center" scope="col">Customer</th>
                        <th class="text-center" scope="col">Order Date</th>
                        <th class="text-center" scope="col">Total (Ex. GST)</th>
                        <th class="text-center" scope="col">Grand Total</th>
                        <th class="text-center" scope="col">Paid</th>
                        <th class="text-center" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->vars['table/result']['rows'] ?? [] as $order): ?>
                    <tr>
                        <td><?=\_\html\val($order,'#') ?></td>
                        <td><?=\_\html\val($order,'order_code') ?></td>
                        <td><?=\_\html\val($order,'vehicle_rego') ?></td>
                        <td><?=\_\html\val($order,'customer_name') ?></td>
                        <td class="text-center"><?=\_\html\val($order,'order_date') ?></td>
                        <td class="text-end"><?=\number_format($order['ex_gst_total'], 2) ?></td>
                        <td class="text-end"><?=\number_format($order['grand_total'], 2) ?></td>
                        <td class="text-center"><?=$order['paid'] ? \date('Y-m-d', \strtotime($order['paid_on'])) : 'Not Paid' ?></td>
                        <td class="text-end">
                            <a href="<?=\_\CTLR_URL?>/item_invoice?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-printer"></i> <!-- Printer Icon -->
                            </a>
                            <a href="<?=\_\CTLR_URL?>/item_edit?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil-fill"></i> <!-- Edit Icon -->
                            </a>
                            <a href="?--action=delete&id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this order?')">
                                <i class="bi bi-trash-fill"></i> <!-- Delete Icon -->
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}; }

