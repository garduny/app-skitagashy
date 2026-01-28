<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$data = getQuery(" SELECT id,name,slug,icon,parent_id FROM categories WHERE is_active=1 ORDER BY parent_id ASC,id ASC ");
encode(['status' => true, 'data' => $data]);
