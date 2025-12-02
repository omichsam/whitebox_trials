[33mcommit f64dde0955ae619300eb5f35f5e0e1b1e9e494a0[m
Author: omichsam <omichsam@gmail.com>
Date:   Mon Dec 1 14:25:28 2025 +0300

    UPDATE : Worked on the authentication process of the code

[1mdiff --git a/.htaccessa b/.htaccessa[m
[1mnew file mode 100644[m
[1mindex 0000000..c151b11[m
[1m--- /dev/null[m
[1m+++ b/.htaccessa[m
[36m@@ -0,0 +1,4 @@[m
[32m+[m[32m#BEGIN WHITEBOX[m
[32m+[m[32mphp_flag display_errors Off[m
[32m+[m[32mOptions -Indexes[m
[32m+[m[32m#END WHITEBOX[m
[1mdiff --git a/.php-preview-router.php b/.php-preview-router.php[m
[1mnew file mode 100644[m
[1mindex 0000000..c451250[m
[1m--- /dev/null[m
[1m+++ b/.php-preview-router.php[m
[36m@@ -0,0 +1,88 @@[m
[32m+[m[32m<?php[m
[32m+[m[32m// Enhanced PHP Router for HTML Preview Extension[m
[32m+[m[32mheader('Access-Control-Allow-Origin: *');[m
[32m+[m[32mheader('Access-Control-Allow-Methods: GET, POST, OPTIONS');[m
[32m+[m[32mheader('Access-Control-Allow-Headers: Content-Type');[m
[32m+[m
[32m+[m[32mif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {[m
[32m+[m[32m    http_response_code(200);[m
[32m+[m[32m    exit();[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m// Security check: Prevent directory traversal[m
[32m+[m[32m$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);[m
[32m+[m[32m$normalizedPath = str_replace('..', '', $requestPath);[m
[32m+[m[32m$filePath = __DIR__ . $normalizedPath;[m
[32m+[m
[32m+[m[32m// Handle form submissions[m
[32m+[m[32mif ($_SERVER['REQUEST_METHOD'] === 'POST') {[m
[32m+[m[32m    $response = [[m
[32m+[m[32m        'method' => 'POST',[m
[32m+[m[32m        'data' => $_POST,[m
[32m+[m[32m        'files' => $_FILES,[m
[32m+[m[32m        'timestamp' => time()[m
[32m+[m[32m    ];[m
[32m+[m[32m    header('Content-Type: application/json');[m
[32m+[m[32m    echo json_encode($response);[m
[32m+[m[32m    exit();[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m// Static file handling[m
[32m+[m[32mif (is_file($filePath)) {[m
[32m+[m[32m    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));[m
[32m+[m[41m    [m
[32m+[m[32m    // Set appropriate content type[m
[32m+[m[32m    $contentTypes = [[m
[32m+[m[32m        'html' => 'text/html',[m
[32m+[m[32m        'php' => 'text/html',[m
[32m+[m[32m        'css' => 'text/css',[m
[32m+[m[32m        'js' => 'application/javascript',[m
[32m+[m[32m        'json' => 'application/json',[m
[32m+[m[32m        'png' => 'image/png',[m
[32m+[m[32m        'jpg' => 'image/jpeg',[m
[32m+[m[32m        'jpeg' => 'image/jpeg',[m
[32m+[m[32m        'gif' => 'image/gif',[m
[32m+[m[32m        'svg' => 'image/svg+xml',[m
[32m+[m[32m    ];[m
[32m+[m[41m    [m
[32m+[m[32m    if (isset($contentTypes[$extension])) {[m
[32m+[m[32m        header('Content-Type: ' . $contentTypes[$extension]);[m
[32m+[m[32m    }[m
[32m+[m[41m    [m
[32m+[m[32m    // Handle PHP files[m
[32m+[m[32m    if ($extension === 'php') {[m
[32m+[m[32m        try {[m
[32m+[m[32m            // Capture output and errors[m
[32m+[m[32m            ob_start();[m
[32m+[m[32m            include($filePath);[m
[32m+[m[32m            $output = ob_get_clean();[m
[32m+[m[41m            [m
[32m+[m[32m            // Add debug information if requested[m
[32m+[m[32m            if (isset($_GET['debug'])) {[m
[32m+[m[32m                $debug = [[m
[32m+[m[32m                    'file' => $filePath,[m
[32m+[m[32m                    'size' => filesize($filePath),[m
[32m+[m[32m                    'modified' => date('Y-m-d H:i:s', filemtime($filePath)),[m
[32m+[m[32m                    'php_version' => PHP_VERSION,[m
[32m+[m[32m                ];[m
[32m+[m[32m                $output .= '\n<!-- Debug Info: ' . json_encode($debug) . ' -->';[m
[32m+[m[32m            }[m
[32m+[m[41m            [m
[32m+[m[32m            echo $output;[m
[32m+[m[32m        } catch (Throwable $e) {[m
[32m+[m[32m            http_response_code(500);[m
[32m+[m[32m            echo '<h1>PHP Error</h1>';[m
[32m+[m[32m            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';[m
[32m+[m[32m        }[m
[32m+[m[32m        exit();[m
[32m+[m[32m    }[m
[32m+[m[41m    [m
[32m+[m[32m    // Serve static files[m
[32m+[m[32m    readfile($filePath);[m
[32m+[m[32m    exit();[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m// Handle 404[m
[32m+[m[32mhttp_response_code(404);[m
[32m+[m[32mecho '<h1>404 Not Found</h1>';[m
[32m+[m[32mecho '<p>The requested file "' . htmlspecialchars($requestPath) . '" was not found.</p>';[m
[1mdiff --git a/DataTables/css/dataTables.bootstrap.css b/DataTables/css/dataTables.bootstrap.css[m
[1mnew file mode 100644[m
[1mindex 0000000..c35a465[m
[1m--- /dev/null[m
[1m+++ b/DataTables/css/dataTables.bootstrap.css[m
[36m@@ -0,0 +1,274 @@[m
[32m+[m[32m@charset "UTF-8";[m
[32m+[m[32mtd.dt-control {[m
[32m+[m[32m  background: url("https://www.datatables.net/examples/resources/details_open.png") no-repeat center center;[m
[32m+[m[32m  cursor: pointer;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtr.dt-hasChild td.dt-control {[m
[32m+[m[32m  background: url("https://www.datatables.net/examples/resources/details_close.png") no-repeat center center;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.dataTable th.dt-left,[m
[32m+[m[32mtable.dataTable td.dt-left {[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-center,[m
[32m+[m[32mtable.dataTable td.dt-center,[m
[32m+[m[32mtable.dataTable td.dataTables_empty {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-right,[m
[32m+[m[32mtable.dataTable td.dt-right {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-justify,[m
[32m+[m[32mtable.dataTable td.dt-justify {[m
[32m+[m[32m  text-align: justify;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-nowrap,[m
[32m+[m[32mtable.dataTable td.dt-nowrap {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-left,[m
[32m+[m[32mtable.dataTable thead td.dt-head-left,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-left,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-left {[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-center,[m
[32m+[m[32mtable.dataTable thead td.dt-head-center,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-center,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-center {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-right,[m
[32m+[m[32mtable.dataTable thead td.dt-head-right,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-right,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-right {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-justify,[m
[32m+[m[32mtable.dataTable thead td.dt-head-justify,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-justify,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-justify {[m
[32m+[m[32m  text-align: justify;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-nowrap,[m
[32m+[m[32mtable.dataTable thead td.dt-head-nowrap,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-nowrap,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-nowrap {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-left,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-left {[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-center,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-center {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-right,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-right {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-justify,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-justify {[m
[32m+[m[32m  text-align: justify;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-nowrap,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-nowrap {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.dataTable {[m
[32m+[m[32m  clear: both;[m
[32m+[m[32m  margin-top: 6px !important;[m
[32m+[m[32m  margin-bottom: 6px !important;[m
[32m+[m[32m  max-width: none !important;[m
[32m+[m[32m  border-collapse: separate !important;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable td,[m
[32m+[m[32mtable.dataTable th {[m
[32m+[m[32m  -webkit-box-sizing: content-box;[m
[32m+[m[32m  box-sizing: content-box;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable td.dataTables_empty,[m
[32m+[m[32mtable.dataTable th.dataTables_empty {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.nowrap th,[m
[32m+[m[32mtable.dataTable.nowrap td {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_length label {[m
[32m+[m[32m  font-weight: normal;[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_length select {[m
[32m+[m[32m  width: 75px;[m
[32m+[m[32m  display: inline-block;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter label {[m
[32m+[m[32m  font-weight: normal;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter input {[m
[32m+[m[32m  margin-left: 0.5em;[m
[32m+[m[32m  display: inline-block;[m
[32m+[m[32m  width: auto;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_info {[m
[32m+[m[32m  padding-top: 8px;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_paginate {[m
[32m+[m[32m  margin: 0;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_paginate ul.pagination {[m
[32m+[m[32m  margin: 2px 0;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_processing {[m
[32m+[m[32m  position: absolute;[m
[32m+[m[32m  top: 50%;[m
[32m+[m[32m  left: 50%;[m
[32m+[m[32m  width: 200px;[m
[32m+[m[32m  margin-left: -100px;[m
[32m+[m[32m  margin-top: -26px;[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m  padding: 1em 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.dataTable thead > tr > th.sorting_asc, table.dataTable thead > tr > th.sorting_desc, table.dataTable thead > tr > th.sorting,[m
[32m+[m[32mtable.dataTable thead > tr > td.sorting_asc,[m
[32m+[m[32mtable.dataTable thead > tr > td.sorting_desc,[m
[32m+[m[32mtable.dataTable thead > tr > td.sorting {[m
[32m+[m[32m  padding-right: 30px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead > tr > th:active,[m
[32m+[m[32mtable.dataTable thead > tr > td:active {[m
[32m+[m[32m  outline: none;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead .sorting,[m
[32m+[m[32mtable.dataTable thead .sorting_asc,[m
[32m+[m[32mtable.dataTable thead .sorting_desc,[m
[32m+[m[32mtable.dataTable thead .sorting_asc_disabled,[m
[32m+[m[32mtable.dataTable thead .sorting_desc_disabled {[m
[32m+[m[32m  cursor: pointer;[m
[32m+[m[32m  position: relative;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead .sorting:after,[m
[32m+[m[32mtable.dataTable thead .sorting_asc:after,[m
[32m+[m[32mtable.dataTable thead .sorting_desc:after,[m
[32m+[m[32mtable.dataTable thead .sorting_asc_disabled:after,[m
[32m+[m[32mtable.dataTable thead .sorting_desc_disabled:after {[m
[32m+[m[32m  position: absolute;[m
[32m+[m[32m  bottom: 8px;[m
[32m+[m[32m  right: 8px;[m
[32m+[m[32m  display: block;[m
[32m+[m[32m  font-family: "Glyphicons Halflings";[m
[32m+[m[32m  opacity: 0.5;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead .sorting:after {[m
[32m+[m[32m  opacity: 0.2;[m
[32m+[m[32m  content: "";[m
[32m+[m[32m  /* sort */[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead .sorting_asc:after {[m
[32m+[m[32m  opacity: 0.5;[m
[32m+[m[32m  content: "";[m
[32m+[m[32m  /* sort-by-attributes */[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead .sorting_desc:after {[m
[32m+[m[32m  opacity: 0.5;[m
[32m+[m[32m  content: "";[m
[32m+[m[32m  /* sort-by-attributes-alt */[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead .sorting_asc_disabled:after,[m
[32m+[m[32mtable.dataTable thead .sorting_desc_disabled:after {[m
[32m+[m[32m  color: #eee;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollHead table.dataTable {[m
[32m+[m[32m  margin-bottom: 0 !important;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollBody > table {[m
[32m+[m[32m  border-top: none;[m
[32m+[m[32m  margin-top: 0 !important;[m
[32m+[m[32m  margin-bottom: 0 !important;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting:after,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_asc:after,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_desc:after {[m
[32m+[m[32m  display: none;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_scrollBody > table > tbody > tr:first-child > th,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > tbody > tr:first-child > td {[m
[32m+[m[32m  border-top: none;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollFoot > .dataTables_scrollFootInner {[m
[32m+[m[32m  box-sizing: content-box;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_scrollFoot > .dataTables_scrollFootInner > table {[m
[32m+[m[32m  margin-top: 0 !important;[m
[32m+[m[32m  border-top: none;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m@media screen and (max-width: 767px) {[m
[32m+[m[32m  div.dataTables_wrapper div.dataTables_length,[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter,[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_info,[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_paginate {[m
[32m+[m[32m    text-align: center;[m
[32m+[m[32m  }[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.table-condensed > thead > tr > th {[m
[32m+[m[32m  padding-right: 20px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.table-condensed .sorting:after,[m
[32m+[m[32mtable.dataTable.table-condensed .sorting_asc:after,[m
[32m+[m[32mtable.dataTable.table-condensed .sorting_desc:after {[m
[32m+[m[32m  top: 6px;[m
[32m+[m[32m  right: 6px;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.table-bordered.dataTable {[m
[32m+[m[32m  border-right-width: 0;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable th,[m
[32m+[m[32mtable.table-bordered.dataTable td {[m
[32m+[m[32m  border-left-width: 0;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable th:last-child, table.table-bordered.dataTable th:last-child,[m
[32m+[m[32mtable.table-bordered.dataTable td:last-child,[m
[32m+[m[32mtable.table-bordered.dataTable td:last-child {[m
[32m+[m[32m  border-right-width: 1px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable tbody th,[m
[32m+[m[32mtable.table-bordered.dataTable tbody td {[m
[32m+[m[32m  border-bottom-width: 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollHead table.table-bordered {[m
[32m+[m[32m  border-bottom-width: 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.table-responsive > div.dataTables_wrapper > div.row {[m
[32m+[m[32m  margin: 0;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.table-responsive > div.dataTables_wrapper > div.row > div[class^=col-]:first-child {[m
[32m+[m[32m  padding-left: 0;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.table-responsive > div.dataTables_wrapper > div.row > div[class^=col-]:last-child {[m
[32m+[m[32m  padding-right: 0;[m
[32m+[m[32m}[m
[1mdiff --git a/DataTables/css/dataTables.bootstrap.min.css b/DataTables/css/dataTables.bootstrap.min.css[m
[1mnew file mode 100644[m
[1mindex 0000000..1d31975[m
[1m--- /dev/null[m
[1m+++ b/DataTables/css/dataTables.bootstrap.min.css[m
[36m@@ -0,0 +1 @@[m
[32m+[m[32mtd.dt-control{background:url("https://www.datatables.net/examples/resources/details_open.png") no-repeat center center;cursor:pointer}tr.dt-hasChild td.dt-control{background:url("https://www.datatables.net/examples/resources/details_close.png") no-repeat center center}table.dataTable th.dt-left,table.dataTable td.dt-left{text-align:left}table.dataTable th.dt-center,table.dataTable td.dt-center,table.dataTable td.dataTables_empty{text-align:center}table.dataTable th.dt-right,table.dataTable td.dt-right{text-align:right}table.dataTable th.dt-justify,table.dataTable td.dt-justify{text-align:justify}table.dataTable th.dt-nowrap,table.dataTable td.dt-nowrap{white-space:nowrap}table.dataTable thead th.dt-head-left,table.dataTable thead td.dt-head-left,table.dataTable tfoot th.dt-head-left,table.dataTable tfoot td.dt-head-left{text-align:left}table.dataTable thead th.dt-head-center,table.dataTable thead td.dt-head-center,table.dataTable tfoot th.dt-head-center,table.dataTable tfoot td.dt-head-center{text-align:center}table.dataTable thead th.dt-head-right,table.dataTable thead td.dt-head-right,table.dataTable tfoot th.dt-head-right,table.dataTable tfoot td.dt-head-right{text-align:right}table.dataTable thead th.dt-head-justify,table.dataTable thead td.dt-head-justify,table.dataTable tfoot th.dt-head-justify,table.dataTable tfoot td.dt-head-justify{text-align:justify}table.dataTable thead th.dt-head-nowrap,table.dataTable thead td.dt-head-nowrap,table.dataTable tfoot th.dt-head-nowrap,table.dataTable tfoot td.dt-head-nowrap{white-space:nowrap}table.dataTable tbody th.dt-body-left,table.dataTable tbody td.dt-body-left{text-align:left}table.dataTable tbody th.dt-body-center,table.dataTable tbody td.dt-body-center{text-align:center}table.dataTable tbody th.dt-body-right,table.dataTable tbody td.dt-body-right{text-align:right}table.dataTable tbody th.dt-body-justify,table.dataTable tbody td.dt-body-justify{text-align:justify}table.dataTable tbody th.dt-body-nowrap,table.dataTable tbody td.dt-body-nowrap{white-space:nowrap}table.dataTable{clear:both;margin-top:6px !important;margin-bottom:6px !important;max-width:none !important;border-collapse:separate !important}table.dataTable td,table.dataTable th{-webkit-box-sizing:content-box;box-sizing:content-box}table.dataTable td.dataTables_empty,table.dataTable th.dataTables_empty{text-align:center}table.dataTable.nowrap th,table.dataTable.nowrap td{white-space:nowrap}div.dataTables_wrapper div.dataTables_length label{font-weight:normal;text-align:left;white-space:nowrap}div.dataTables_wrapper div.dataTables_length select{width:75px;display:inline-block}div.dataTables_wrapper div.dataTables_filter{text-align:right}div.dataTables_wrapper div.dataTables_filter label{font-weight:normal;white-space:nowrap;text-align:left}div.dataTables_wrapper div.dataTables_filter input{margin-left:.5em;display:inline-block;width:auto}div.dataTables_wrapper div.dataTables_info{padding-top:8px;white-space:nowrap}div.dataTables_wrapper div.dataTables_paginate{margin:0;white-space:nowrap;text-align:right}div.dataTables_wrapper div.dataTables_paginate ul.pagination{margin:2px 0;white-space:nowrap}div.dataTables_wrapper div.dataTables_processing{position:absolute;top:50%;left:50%;width:200px;margin-left:-100px;margin-top:-26px;text-align:center;padding:1em 0}table.dataTable thead>tr>th.sorting_asc,table.dataTable thead>tr>th.sorting_desc,table.dataTable thead>tr>th.sorting,table.dataTable thead>tr>td.sorting_asc,table.dataTable thead>tr>td.sorting_desc,table.dataTable thead>tr>td.sorting{padding-right:30px}table.dataTable thead>tr>th:active,table.dataTable thead>tr>td:active{outline:none}table.dataTable thead .sorting,table.dataTable thead .sorting_asc,table.dataTable thead .sorting_desc,table.dataTable thead .sorting_asc_disabled,table.dataTable thead .sorting_desc_disabled{cursor:pointer;position:relative}table.dataTable thead .sorting:after,table.dataTable thead .sorting_asc:after,table.dataTable thead .sorting_desc:after,table.dataTable thead .sorting_asc_disabled:after,table.dataTable thead .sorting_desc_disabled:after{position:absolute;bottom:8px;right:8px;display:block;font-family:"Glyphicons Halflings";opacity:.5}table.dataTable thead .sorting:after{opacity:.2;content:""}table.dataTable thead .sorting_asc:after{opacity:.5;content:""}table.dataTable thead .sorting_desc:after{opacity:.5;content:""}table.dataTable thead .sorting_asc_disabled:after,table.dataTable thead .sorting_desc_disabled:after{color:#eee}div.dataTables_scrollHead table.dataTable{margin-bottom:0 !important}div.dataTables_scrollBody>table{border-top:none;margin-top:0 !important;margin-bottom:0 !important}div.dataTables_scrollBody>table>thead .sorting:after,div.dataTables_scrollBody>table>thead .sorting_asc:after,div.dataTables_scrollBody>table>thead .sorting_desc:after{display:none}div.dataTables_scrollBody>table>tbody>tr:first-child>th,div.dataTables_scrollBody>table>tbody>tr:first-child>td{border-top:none}div.dataTables_scrollFoot>.dataTables_scrollFootInner{box-sizing:content-box}div.dataTables_scrollFoot>.dataTables_scrollFootInner>table{margin-top:0 !important;border-top:none}@media screen and (max-width: 767px){div.dataTables_wrapper div.dataTables_length,div.dataTables_wrapper div.dataTables_filter,div.dataTables_wrapper div.dataTables_info,div.dataTables_wrapper div.dataTables_paginate{text-align:center}}table.dataTable.table-condensed>thead>tr>th{padding-right:20px}table.dataTable.table-condensed .sorting:after,table.dataTable.table-condensed .sorting_asc:after,table.dataTable.table-condensed .sorting_desc:after{top:6px;right:6px}table.table-bordered.dataTable{border-right-width:0}table.table-bordered.dataTable th,table.table-bordered.dataTable td{border-left-width:0}table.table-bordered.dataTable th:last-child,table.table-bordered.dataTable th:last-child,table.table-bordered.dataTable td:last-child,table.table-bordered.dataTable td:last-child{border-right-width:1px}table.table-bordered.dataTable tbody th,table.table-bordered.dataTable tbody td{border-bottom-width:0}div.dataTables_scrollHead table.table-bordered{border-bottom-width:0}div.table-responsive>div.dataTables_wrapper>div.row{margin:0}div.table-responsive>div.dataTables_wrapper>div.row>div[class^=col-]:first-child{padding-left:0}div.table-responsive>div.dataTables_wrapper>div.row>div[class^=col-]:last-child{padding-right:0}[m
[1mdiff --git a/DataTables/css/dataTables.bootstrap4.css b/DataTables/css/dataTables.bootstrap4.css[m
[1mnew file mode 100644[m
[1mindex 0000000..c517e3c[m
[1m--- /dev/null[m
[1m+++ b/DataTables/css/dataTables.bootstrap4.css[m
[36m@@ -0,0 +1,291 @@[m
[32m+[m[32m@charset "UTF-8";[m
[32m+[m[32mtd.dt-control {[m
[32m+[m[32m  background: url("https://www.datatables.net/examples/resources/details_open.png") no-repeat center center;[m
[32m+[m[32m  cursor: pointer;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtr.dt-hasChild td.dt-control {[m
[32m+[m[32m  background: url("https://www.datatables.net/examples/resources/details_close.png") no-repeat center center;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.dataTable th.dt-left,[m
[32m+[m[32mtable.dataTable td.dt-left {[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-center,[m
[32m+[m[32mtable.dataTable td.dt-center,[m
[32m+[m[32mtable.dataTable td.dataTables_empty {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-right,[m
[32m+[m[32mtable.dataTable td.dt-right {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-justify,[m
[32m+[m[32mtable.dataTable td.dt-justify {[m
[32m+[m[32m  text-align: justify;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-nowrap,[m
[32m+[m[32mtable.dataTable td.dt-nowrap {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-left,[m
[32m+[m[32mtable.dataTable thead td.dt-head-left,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-left,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-left {[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-center,[m
[32m+[m[32mtable.dataTable thead td.dt-head-center,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-center,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-center {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-right,[m
[32m+[m[32mtable.dataTable thead td.dt-head-right,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-right,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-right {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-justify,[m
[32m+[m[32mtable.dataTable thead td.dt-head-justify,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-justify,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-justify {[m
[32m+[m[32m  text-align: justify;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-nowrap,[m
[32m+[m[32mtable.dataTable thead td.dt-head-nowrap,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-nowrap,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-nowrap {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-left,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-left {[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-center,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-center {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-right,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-right {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-justify,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-justify {[m
[32m+[m[32m  text-align: justify;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-nowrap,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-nowrap {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.dataTable {[m
[32m+[m[32m  clear: both;[m
[32m+[m[32m  margin-top: 6px !important;[m
[32m+[m[32m  margin-bottom: 6px !important;[m
[32m+[m[32m  max-width: none !important;[m
[32m+[m[32m  border-collapse: separate !important;[m
[32m+[m[32m  border-spacing: 0;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable td,[m
[32m+[m[32mtable.dataTable th {[m
[32m+[m[32m  -webkit-box-sizing: content-box;[m
[32m+[m[32m  box-sizing: content-box;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable td.dataTables_empty,[m
[32m+[m[32mtable.dataTable th.dataTables_empty {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.nowrap th,[m
[32m+[m[32mtable.dataTable.nowrap td {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_length label {[m
[32m+[m[32m  font-weight: normal;[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_length select {[m
[32m+[m[32m  width: auto;[m
[32m+[m[32m  display: inline-block;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter label {[m
[32m+[m[32m  font-weight: normal;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter input {[m
[32m+[m[32m  margin-left: 0.5em;[m
[32m+[m[32m  display: inline-block;[m
[32m+[m[32m  width: auto;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_info {[m
[32m+[m[32m  padding-top: 0.85em;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_paginate {[m
[32m+[m[32m  margin: 0;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_paginate ul.pagination {[m
[32m+[m[32m  margin: 2px 0;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m  justify-content: flex-end;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_processing {[m
[32m+[m[32m  position: absolute;[m
[32m+[m[32m  top: 50%;[m
[32m+[m[32m  left: 50%;[m
[32m+[m[32m  width: 200px;[m
[32m+[m[32m  margin-left: -100px;[m
[32m+[m[32m  margin-top: -26px;[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m  padding: 1em 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.dataTable > thead > tr > th:active,[m
[32m+[m[32mtable.dataTable > thead > tr > td:active {[m
[32m+[m[32m  outline: none;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead > tr > th:not(.sorting_disabled),[m
[32m+[m[32mtable.dataTable > thead > tr > td:not(.sorting_disabled) {[m
[32m+[m[32m  padding-right: 30px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled {[m
[32m+[m[32m  cursor: pointer;[m
[32m+[m[32m  position: relative;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting:before, table.dataTable > thead .sorting:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:after {[m
[32m+[m[32m  position: absolute;[m
[32m+[m[32m  bottom: 0.9em;[m
[32m+[m[32m  display: block;[m
[32m+[m[32m  opacity: 0.3;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:before {[m
[32m+[m[32m  right: 1em;[m
[32m+[m[32m  content: "↑";[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:after {[m
[32m+[m[32m  right: 0.5em;[m
[32m+[m[32m  content: "↓";[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:after {[m
[32m+[m[32m  opacity: 1;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:after {[m
[32m+[m[32m  opacity: 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollHead table.dataTable {[m
[32m+[m[32m  margin-bottom: 0 !important;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollBody > table {[m
[32m+[m[32m  border-top: none;[m
[32m+[m[32m  margin-top: 0 !important;[m
[32m+[m[32m  margin-bottom: 0 !important;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting:before,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_asc:before,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_desc:before,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting:after,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_asc:after,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_desc:after {[m
[32m+[m[32m  display: none;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_scrollBody > table > tbody tr:first-child th,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > tbody tr:first-child td {[m
[32m+[m[32m  border-top: none;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollFoot > .dataTables_scrollFootInner {[m
[32m+[m[32m  box-sizing: content-box;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_scrollFoot > .dataTables_scrollFootInner > table {[m
[32m+[m[32m  margin-top: 0 !important;[m
[32m+[m[32m  border-top: none;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m@media screen and (max-width: 767px) {[m
[32m+[m[32m  div.dataTables_wrapper div.dataTables_length,[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter,[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_info,[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_paginate {[m
[32m+[m[32m    text-align: center;[m
[32m+[m[32m  }[m
[32m+[m[32m  div.dataTables_wrapper div.dataTables_paginate ul.pagination {[m
[32m+[m[32m    justify-content: center !important;[m
[32m+[m[32m  }[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.table-sm > thead > tr > th:not(.sorting_disabled) {[m
[32m+[m[32m  padding-right: 20px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.table-sm .sorting:before,[m
[32m+[m[32mtable.dataTable.table-sm .sorting_asc:before,[m
[32m+[m[32mtable.dataTable.table-sm .sorting_desc:before {[m
[32m+[m[32m  top: 5px;[m
[32m+[m[32m  right: 0.85em;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.table-sm .sorting:after,[m
[32m+[m[32mtable.dataTable.table-sm .sorting_asc:after,[m
[32m+[m[32mtable.dataTable.table-sm .sorting_desc:after {[m
[32m+[m[32m  top: 5px;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.table-bordered.dataTable {[m
[32m+[m[32m  border-right-width: 0;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable th,[m
[32m+[m[32mtable.table-bordered.dataTable td {[m
[32m+[m[32m  border-left-width: 0;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable th:last-child, table.table-bordered.dataTable th:last-child,[m
[32m+[m[32mtable.table-bordered.dataTable td:last-child,[m
[32m+[m[32mtable.table-bordered.dataTable td:last-child {[m
[32m+[m[32m  border-right-width: 1px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable tbody th,[m
[32m+[m[32mtable.table-bordered.dataTable tbody td {[m
[32m+[m[32m  border-bottom-width: 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollHead table.table-bordered {[m
[32m+[m[32m  border-bottom-width: 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.table-responsive > div.dataTables_wrapper > div.row {[m
[32m+[m[32m  margin: 0;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.table-responsive > div.dataTables_wrapper > div.row > div[class^=col-]:first-child {[m
[32m+[m[32m  padding-left: 0;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.table-responsive > div.dataTables_wrapper > div.row > div[class^=col-]:last-child {[m
[32m+[m[32m  padding-right: 0;[m
[32m+[m[32m}[m
[1mdiff --git a/DataTables/css/dataTables.bootstrap4.min.css b/DataTables/css/dataTables.bootstrap4.min.css[m
[1mnew file mode 100644[m
[1mindex 0000000..92992ac[m
[1m--- /dev/null[m
[1m+++ b/DataTables/css/dataTables.bootstrap4.min.css[m
[36m@@ -0,0 +1 @@[m
[32m+[m[32mtd.dt-control{background:url("https://www.datatables.net/examples/resources/details_open.png") no-repeat center center;cursor:pointer}tr.dt-hasChild td.dt-control{background:url("https://www.datatables.net/examples/resources/details_close.png") no-repeat center center}table.dataTable th.dt-left,table.dataTable td.dt-left{text-align:left}table.dataTable th.dt-center,table.dataTable td.dt-center,table.dataTable td.dataTables_empty{text-align:center}table.dataTable th.dt-right,table.dataTable td.dt-right{text-align:right}table.dataTable th.dt-justify,table.dataTable td.dt-justify{text-align:justify}table.dataTable th.dt-nowrap,table.dataTable td.dt-nowrap{white-space:nowrap}table.dataTable thead th.dt-head-left,table.dataTable thead td.dt-head-left,table.dataTable tfoot th.dt-head-left,table.dataTable tfoot td.dt-head-left{text-align:left}table.dataTable thead th.dt-head-center,table.dataTable thead td.dt-head-center,table.dataTable tfoot th.dt-head-center,table.dataTable tfoot td.dt-head-center{text-align:center}table.dataTable thead th.dt-head-right,table.dataTable thead td.dt-head-right,table.dataTable tfoot th.dt-head-right,table.dataTable tfoot td.dt-head-right{text-align:right}table.dataTable thead th.dt-head-justify,table.dataTable thead td.dt-head-justify,table.dataTable tfoot th.dt-head-justify,table.dataTable tfoot td.dt-head-justify{text-align:justify}table.dataTable thead th.dt-head-nowrap,table.dataTable thead td.dt-head-nowrap,table.dataTable tfoot th.dt-head-nowrap,table.dataTable tfoot td.dt-head-nowrap{white-space:nowrap}table.dataTable tbody th.dt-body-left,table.dataTable tbody td.dt-body-left{text-align:left}table.dataTable tbody th.dt-body-center,table.dataTable tbody td.dt-body-center{text-align:center}table.dataTable tbody th.dt-body-right,table.dataTable tbody td.dt-body-right{text-align:right}table.dataTable tbody th.dt-body-justify,table.dataTable tbody td.dt-body-justify{text-align:justify}table.dataTable tbody th.dt-body-nowrap,table.dataTable tbody td.dt-body-nowrap{white-space:nowrap}table.dataTable{clear:both;margin-top:6px !important;margin-bottom:6px !important;max-width:none !important;border-collapse:separate !important;border-spacing:0}table.dataTable td,table.dataTable th{-webkit-box-sizing:content-box;box-sizing:content-box}table.dataTable td.dataTables_empty,table.dataTable th.dataTables_empty{text-align:center}table.dataTable.nowrap th,table.dataTable.nowrap td{white-space:nowrap}div.dataTables_wrapper div.dataTables_length label{font-weight:normal;text-align:left;white-space:nowrap}div.dataTables_wrapper div.dataTables_length select{width:auto;display:inline-block}div.dataTables_wrapper div.dataTables_filter{text-align:right}div.dataTables_wrapper div.dataTables_filter label{font-weight:normal;white-space:nowrap;text-align:left}div.dataTables_wrapper div.dataTables_filter input{margin-left:.5em;display:inline-block;width:auto}div.dataTables_wrapper div.dataTables_info{padding-top:.85em}div.dataTables_wrapper div.dataTables_paginate{margin:0;white-space:nowrap;text-align:right}div.dataTables_wrapper div.dataTables_paginate ul.pagination{margin:2px 0;white-space:nowrap;justify-content:flex-end}div.dataTables_wrapper div.dataTables_processing{position:absolute;top:50%;left:50%;width:200px;margin-left:-100px;margin-top:-26px;text-align:center;padding:1em 0}table.dataTable>thead>tr>th:active,table.dataTable>thead>tr>td:active{outline:none}table.dataTable>thead>tr>th:not(.sorting_disabled),table.dataTable>thead>tr>td:not(.sorting_disabled){padding-right:30px}table.dataTable>thead .sorting,table.dataTable>thead .sorting_asc,table.dataTable>thead .sorting_desc,table.dataTable>thead .sorting_asc_disabled,table.dataTable>thead .sorting_desc_disabled{cursor:pointer;position:relative}table.dataTable>thead .sorting:before,table.dataTable>thead .sorting:after,table.dataTable>thead .sorting_asc:before,table.dataTable>thead .sorting_asc:after,table.dataTable>thead .sorting_desc:before,table.dataTable>thead .sorting_desc:after,table.dataTable>thead .sorting_asc_disabled:before,table.dataTable>thead .sorting_asc_disabled:after,table.dataTable>thead .sorting_desc_disabled:before,table.dataTable>thead .sorting_desc_disabled:after{position:absolute;bottom:.9em;display:block;opacity:.3}table.dataTable>thead .sorting:before,table.dataTable>thead .sorting_asc:before,table.dataTable>thead .sorting_desc:before,table.dataTable>thead .sorting_asc_disabled:before,table.dataTable>thead .sorting_desc_disabled:before{right:1em;content:"↑"}table.dataTable>thead .sorting:after,table.dataTable>thead .sorting_asc:after,table.dataTable>thead .sorting_desc:after,table.dataTable>thead .sorting_asc_disabled:after,table.dataTable>thead .sorting_desc_disabled:after{right:.5em;content:"↓"}table.dataTable>thead .sorting_asc:before,table.dataTable>thead .sorting_desc:after{opacity:1}table.dataTable>thead .sorting_asc_disabled:before,table.dataTable>thead .sorting_desc_disabled:after{opacity:0}div.dataTables_scrollHead table.dataTable{margin-bottom:0 !important}div.dataTables_scrollBody>table{border-top:none;margin-top:0 !important;margin-bottom:0 !important}div.dataTables_scrollBody>table>thead .sorting:before,div.dataTables_scrollBody>table>thead .sorting_asc:before,div.dataTables_scrollBody>table>thead .sorting_desc:before,div.dataTables_scrollBody>table>thead .sorting:after,div.dataTables_scrollBody>table>thead .sorting_asc:after,div.dataTables_scrollBody>table>thead .sorting_desc:after{display:none}div.dataTables_scrollBody>table>tbody tr:first-child th,div.dataTables_scrollBody>table>tbody tr:first-child td{border-top:none}div.dataTables_scrollFoot>.dataTables_scrollFootInner{box-sizing:content-box}div.dataTables_scrollFoot>.dataTables_scrollFootInner>table{margin-top:0 !important;border-top:none}@media screen and (max-width: 767px){div.dataTables_wrapper div.dataTables_length,div.dataTables_wrapper div.dataTables_filter,div.dataTables_wrapper div.dataTables_info,div.dataTables_wrapper div.dataTables_paginate{text-align:center}div.dataTables_wrapper div.dataTables_paginate ul.pagination{justify-content:center !important}}table.dataTable.table-sm>thead>tr>th:not(.sorting_disabled){padding-right:20px}table.dataTable.table-sm .sorting:before,table.dataTable.table-sm .sorting_asc:before,table.dataTable.table-sm .sorting_desc:before{top:5px;right:.85em}table.dataTable.table-sm .sorting:after,table.dataTable.table-sm .sorting_asc:after,table.dataTable.table-sm .sorting_desc:after{top:5px}table.table-bordered.dataTable{border-right-width:0}table.table-bordered.dataTable th,table.table-bordered.dataTable td{border-left-width:0}table.table-bordered.dataTable th:last-child,table.table-bordered.dataTable th:last-child,table.table-bordered.dataTable td:last-child,table.table-bordered.dataTable td:last-child{border-right-width:1px}table.table-bordered.dataTable tbody th,table.table-bordered.dataTable tbody td{border-bottom-width:0}div.dataTables_scrollHead table.table-bordered{border-bottom-width:0}div.table-responsive>div.dataTables_wrapper>div.row{margin:0}div.table-responsive>div.dataTables_wrapper>div.row>div[class^=col-]:first-child{padding-left:0}div.table-responsive>div.dataTables_wrapper>div.row>div[class^=col-]:last-child{padding-right:0}[m
[1mdiff --git a/DataTables/css/dataTables.bootstrap5.css b/DataTables/css/dataTables.bootstrap5.css[m
[1mnew file mode 100644[m
[1mindex 0000000..3b84bef[m
[1m--- /dev/null[m
[1m+++ b/DataTables/css/dataTables.bootstrap5.css[m
[36m@@ -0,0 +1,312 @@[m
[32m+[m[32m@charset "UTF-8";[m
[32m+[m[32mtd.dt-control {[m
[32m+[m[32m  background: url("https://www.datatables.net/examples/resources/details_open.png") no-repeat center center;[m
[32m+[m[32m  cursor: pointer;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtr.dt-hasChild td.dt-control {[m
[32m+[m[32m  background: url("https://www.datatables.net/examples/resources/details_close.png") no-repeat center center;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.dataTable th.dt-left,[m
[32m+[m[32mtable.dataTable td.dt-left {[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-center,[m
[32m+[m[32mtable.dataTable td.dt-center,[m
[32m+[m[32mtable.dataTable td.dataTables_empty {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-right,[m
[32m+[m[32mtable.dataTable td.dt-right {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-justify,[m
[32m+[m[32mtable.dataTable td.dt-justify {[m
[32m+[m[32m  text-align: justify;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable th.dt-nowrap,[m
[32m+[m[32mtable.dataTable td.dt-nowrap {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-left,[m
[32m+[m[32mtable.dataTable thead td.dt-head-left,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-left,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-left {[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-center,[m
[32m+[m[32mtable.dataTable thead td.dt-head-center,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-center,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-center {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-right,[m
[32m+[m[32mtable.dataTable thead td.dt-head-right,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-right,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-right {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-justify,[m
[32m+[m[32mtable.dataTable thead td.dt-head-justify,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-justify,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-justify {[m
[32m+[m[32m  text-align: justify;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable thead th.dt-head-nowrap,[m
[32m+[m[32mtable.dataTable thead td.dt-head-nowrap,[m
[32m+[m[32mtable.dataTable tfoot th.dt-head-nowrap,[m
[32m+[m[32mtable.dataTable tfoot td.dt-head-nowrap {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-left,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-left {[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-center,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-center {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-right,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-right {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-justify,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-justify {[m
[32m+[m[32m  text-align: justify;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable tbody th.dt-body-nowrap,[m
[32m+[m[32mtable.dataTable tbody td.dt-body-nowrap {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m/*! Bootstrap 5 integration for DataTables[m
[32m+[m[32m *[m
[32m+[m[32m * ©2020 SpryMedia Ltd, all rights reserved.[m
[32m+[m[32m * License: MIT datatables.net/license/mit[m
[32m+[m[32m */[m
[32m+[m[32mtable.dataTable {[m
[32m+[m[32m  clear: both;[m
[32m+[m[32m  margin-top: 6px !important;[m
[32m+[m[32m  margin-bottom: 6px !important;[m
[32m+[m[32m  max-width: none !important;[m
[32m+[m[32m  border-collapse: separate !important;[m
[32m+[m[32m  border-spacing: 0;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable td,[m
[32m+[m[32mtable.dataTable th {[m
[32m+[m[32m  -webkit-box-sizing: content-box;[m
[32m+[m[32m  box-sizing: content-box;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable td.dataTables_empty,[m
[32m+[m[32mtable.dataTable th.dataTables_empty {[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.nowrap th,[m
[32m+[m[32mtable.dataTable.nowrap td {[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_length label {[m
[32m+[m[32m  font-weight: normal;[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_length select {[m
[32m+[m[32m  width: auto;[m
[32m+[m[32m  display: inline-block;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter {[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter label {[m
[32m+[m[32m  font-weight: normal;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m  text-align: left;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter input {[m
[32m+[m[32m  margin-left: 0.5em;[m
[32m+[m[32m  display: inline-block;[m
[32m+[m[32m  width: auto;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_info {[m
[32m+[m[32m  padding-top: 0.85em;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_paginate {[m
[32m+[m[32m  margin: 0;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m  text-align: right;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_paginate ul.pagination {[m
[32m+[m[32m  margin: 2px 0;[m
[32m+[m[32m  white-space: nowrap;[m
[32m+[m[32m  justify-content: flex-end;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_processing {[m
[32m+[m[32m  position: absolute;[m
[32m+[m[32m  top: 50%;[m
[32m+[m[32m  left: 50%;[m
[32m+[m[32m  width: 200px;[m
[32m+[m[32m  margin-left: -100px;[m
[32m+[m[32m  margin-top: -26px;[m
[32m+[m[32m  text-align: center;[m
[32m+[m[32m  padding: 1em 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.dataTable > thead > tr > th:active,[m
[32m+[m[32mtable.dataTable > thead > tr > td:active {[m
[32m+[m[32m  outline: none;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead > tr > th:not(.sorting_disabled),[m
[32m+[m[32mtable.dataTable > thead > tr > td:not(.sorting_disabled) {[m
[32m+[m[32m  padding-right: 30px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled {[m
[32m+[m[32m  cursor: pointer;[m
[32m+[m[32m  position: relative;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting:before, table.dataTable > thead .sorting:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:after {[m
[32m+[m[32m  position: absolute;[m
[32m+[m[32m  bottom: 0.5em;[m
[32m+[m[32m  display: block;[m
[32m+[m[32m  opacity: 0.3;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:before {[m
[32m+[m[32m  right: 1em;[m
[32m+[m[32m  content: "↑";[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:after,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:after {[m
[32m+[m[32m  right: 0.5em;[m
[32m+[m[32m  content: "↓";[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting_asc:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc:after {[m
[32m+[m[32m  opacity: 1;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable > thead .sorting_asc_disabled:before,[m
[32m+[m[32mtable.dataTable > thead .sorting_desc_disabled:after {[m
[32m+[m[32m  opacity: 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollHead table.dataTable {[m
[32m+[m[32m  margin-bottom: 0 !important;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollBody > table {[m
[32m+[m[32m  border-top: none;[m
[32m+[m[32m  margin-top: 0 !important;[m
[32m+[m[32m  margin-bottom: 0 !important;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting:before,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_asc:before,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_desc:before,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting:after,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_asc:after,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > thead .sorting_desc:after {[m
[32m+[m[32m  display: none;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_scrollBody > table > tbody tr:first-child th,[m
[32m+[m[32mdiv.dataTables_scrollBody > table > tbody tr:first-child td {[m
[32m+[m[32m  border-top: none;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollFoot > .dataTables_scrollFootInner {[m
[32m+[m[32m  box-sizing: content-box;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.dataTables_scrollFoot > .dataTables_scrollFootInner > table {[m
[32m+[m[32m  margin-top: 0 !important;[m
[32m+[m[32m  border-top: none;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32m@media screen and (max-width: 767px) {[m
[32m+[m[32m  div.dataTables_wrapper div.dataTables_length,[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_filter,[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_info,[m
[32m+[m[32mdiv.dataTables_wrapper div.dataTables_paginate {[m
[32m+[m[32m    text-align: center;[m
[32m+[m[32m  }[m
[32m+[m[32m  div.dataTables_wrapper div.dataTables_paginate ul.pagination {[m
[32m+[m[32m    justify-content: center !important;[m
[32m+[m[32m  }[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.table-sm > thead > tr > th:not(.sorting_disabled) {[m
[32m+[m[32m  padding-right: 20px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.table-sm .sorting:before,[m
[32m+[m[32mtable.dataTable.table-sm .sorting_asc:before,[m
[32m+[m[32mtable.dataTable.table-sm .sorting_desc:before {[m
[32m+[m[32m  top: 5px;[m
[32m+[m[32m  right: 0.85em;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.table-sm .sorting:after,[m
[32m+[m[32mtable.dataTable.table-sm .sorting_asc:after,[m
[32m+[m[32mtable.dataTable.table-sm .sorting_desc:after {[m
[32m+[m[32m  top: 5px;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.table-bordered.dataTable {[m
[32m+[m[32m  border-right-width: 0;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable thead tr:first-child th,[m
[32m+[m[32mtable.table-bordered.dataTable thead tr:first-child td {[m
[32m+[m[32m  border-top-width: 1px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable th,[m
[32m+[m[32mtable.table-bordered.dataTable td {[m
[32m+[m[32m  border-left-width: 0;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable th:first-child, table.table-bordered.dataTable th:first-child,[m
[32m+[m[32mtable.table-bordered.dataTable td:first-child,[m
[32m+[m[32mtable.table-bordered.dataTable td:first-child {[m
[32m+[m[32m  border-left-width: 1px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable th:last-child, table.table-bordered.dataTable th:last-child,[m
[32m+[m[32mtable.table-bordered.dataTable td:last-child,[m
[32m+[m[32mtable.table-bordered.dataTable td:last-child {[m
[32m+[m[32m  border-right-width: 1px;[m
[32m+[m[32m}[m
[32m+[m[32mtable.table-bordered.dataTable th,[m
[32m+[m[32mtable.table-bordered.dataTable td {[m
[32m+[m[32m  border-bottom-width: 1px;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.dataTables_scrollHead table.table-bordered {[m
[32m+[m[32m  border-bottom-width: 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mdiv.table-responsive > div.dataTables_wrapper > div.row {[m
[32m+[m[32m  margin: 0;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.table-responsive > div.dataTables_wrapper > div.row > div[class^=col-]:first-child {[m
[32m+[m[32m  padding-left: 0;[m
[32m+[m[32m}[m
[32m+[m[32mdiv.table-responsive > div.dataTables_wrapper > div.row > div[class^=col-]:last-child {[m
[32m+[m[32m  padding-right: 0;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.dataTable.table-striped > tbody > tr:nth-of-type(2n+1) {[m
[32m+[m[32m  --bs-table-accent-bg: transparent;[m
[32m+[m[32m}[m
[32m+[m[32mtable.dataTable.table-striped > tbody > tr.odd {[m
[32m+[m[32m  --bs-table-accent-bg: var(--bs-table-striped-bg);[m
[32m+[m[32m}[m
[1mdiff --git a/DataTables/css/dataTables.bootstrap5.min.css b/DataTables/css/dataTables.bootstrap5.min.css[m
[1mnew file mode 100644[m
[1mindex 0000000..8bc9ee9[m
[1m--- /dev/null[m
[1m+++ b/DataTables/css/dataTables.bootstrap5.min.css[m
[36m@@ -0,0 +1,5 @@[m
[32m+[m[32mtd.dt-control{background:url("https://www.datatables.net/examples/resources/details_open.png") no-repeat center center;cursor:pointer}tr.dt-hasChild td.dt-control{background:url("https://www.datatables.net/examples/resources/details_close.png") no-repeat center center}table.dataTable th.dt-left,table.dataTable td.dt-left{text-align:left}table.dataTable th.dt-center,table.dataTable td.dt-center,table.dataTable td.dataTables_empty{text-align:center}table.dataTable th.dt-right,table.dataTable td.dt-right{text-align:right}table.dataTable th.dt-justify,table.dataTable td.dt-justify{text-align:justify}table.dataTable th.dt-nowrap,table.dataTable td.dt-nowrap{white-space:nowrap}table.dataTable thead th.dt-head-left,table.dataTable thead td.dt-head-left,table.dataTable tfoot th.dt-head-left,table.dataTable tfoot td.dt-head-left{text-align:left}table.dataTable thead th.dt-head-center,table.dataTable thead td.dt-head-center,table.dataTable tfoot th.dt-head-center,table.dataTable tfoot td.dt-head-center{text-align:center}table.dataTable thead th.dt-head-right,table.dataTable thead td.dt-head-right,table.dataTable tfoot th.dt-head-right,table.dataTable tfoot td.dt-head-right{text-align:right}table.dataTable thead th.dt-head-justify,table.dataTable thead td.dt-head-justify,table.dataTable tfoot th.dt-head-justify,table.dataTable tfoot td.dt-head-justify{text-align:justify}table.dataTable thead th.dt-head-nowrap,table.dataTable thead td.dt-head-nowrap,table.dataTable tfoot th.dt-head-nowrap,table.dataTable tfoot td.dt-head-nowrap{white-space:nowrap}table.dataTable tbody th.dt-body-left,table.dataTable tbody td.dt-body-left{text-align:left}table.dataTable tbody th.dt-body-center,table.dataTable tbody td.dt-body-center{text-align:center}table.dataTable tbody th.dt-body-right,table.dataTable tbody td.dt-body-right{text-align:right}table.dataTable tbody th.dt-body-justify,table.dataTable tbody td.dt-body-justify{text-align:justify}table.dataTable tbody th.dt-body-nowrap,table.dataTable tbody td.dt-body-nowrap{white-space:nowrap}/*! Bootstrap 5 integration for DataTables[m
[32m+[m[32m *[m
[32m+[m[32m * ©2020 SpryMedia Ltd, all rights reserved.[m
[32m+[m[32m * License: MIT datatables.net/license/mit[m
[32m+[m[32m */table.dataTable{clear:both;margin-top:6px !important;margin-bottom:6px !important;max-width:none !important;border-collapse:separate !important;border-spacing:0}table.dataTable td,table.dataTable th{-webkit-box-sizing:content-box;box-sizing:content-box}table.dataTable td.dataTables_empty,table.dataTable th.dataTables_empty{text-align:center}table.dataTable.nowrap th,table.dataTable.nowrap td{white-space:nowrap}div.dataTables_wrapper div.dataTables_length label{font-weight:normal;text-align:left;white-space:nowrap}div.dataTables_wrapper div.dataTables_length select{width:auto;display:inline-block}div.dataTables_wrapper div.dataTables_filter{text-align:right}div.dataTables_wrapper div.dataTables_filter label{font-weight:normal;white-space:nowrap;text-align:left}div.dataTables_wrapper div.dataTables_filter input{margin-left:.5em;display:inline-block;width:auto}div.dataTables_wrapper div.dataTables_info{padding-top:.85em}div.dataTables_wrapper div.dataTables_paginate{margin:0;white-space:nowrap;text-align:right}div.dataTables_wrapper div.dataTables_paginate ul.pagination{margin:2px 0;white-space:nowrap;justify-content:flex-end}div.dataTables_wrapper div.dataTables_processing{position:absolute;top:50%;left:50%;width:200px;margin-left:-100px;margin-top:-26px;text-align:center;padding:1em 0}table.dataTable>thead>tr>th:active,table.dataTable>thead>tr>td:active{outline:none}table.dataTable>thead>tr>th:not(.sorting_disabled),table.dataTable>thead>tr>td:not(.sorting_disabled){padding-right:30px}table.dataTable>thead .sorting,table.dataTable>thead .sorting_asc,table.dataTable>thead .sorting_desc,table.dataTable>thead .sorting_asc_disabled,table.dataTable>thead .sorting_desc_disabled{cursor:pointer;position:relative}table.dataTable>thead .sorting:before,table.dataTable>thead .sorting:after,table.dataTable>thead .sorting_asc:before,table.dataTable>thead .sorting_asc:after,table.dataTable>thead .sorting_desc:before,table.dataTable>thead .sorting_desc:after,table.dataTable>thead .sorting_asc_disabled:before,table.dataTable>thead .sorting_asc_disabled:after,table.dataTable>thead .sorting_desc_disabled:before,table.dataTable>thead .sorting_desc_disabled:after{position:absolute;bottom:.5em;display:block;opacity:.3}table.dataTable>thead .sorting:before,table.dataTable>thead .sorting_asc:before,table.dataTable>thead .sorting_desc:before,table.dataTable>thead .sorting_asc_disabled:before,table.dataTable>thead .sorting_desc_disabled:before{right:1em;content:"↑"}table.dataTable>thead .sorting:after,table.dataTable>thead .sorting_asc:after,table.dataTable>thead .sorting_desc:after,table.dataTable>thead .sorting_asc_disabled:after,table.dataTable>thead .sorting_desc_disabled:after{right:.5em;content:"↓"}table.dataTable>thead .sorting_asc:before,table.dataTable>thead .sorting_desc:after{opacity:1}table.dataTable>thead .sorting_asc_disabled:before,table.dataTable>thead .sorting_desc_disabled:after{opacity:0}div.dataTables_scrollHead table.dataTable{margin-bottom:0 !important}div.dataTables_scrollBody>table{border-top:none;margin-top:0 !important;margin-bottom:0 !important}div.dataTables_scrollBody>table>thead .sorting:before,div.dataTables_scrollBody>table>thead .sorting_asc:before,div.dataTables_scrollBody>table>thead .sorting_desc:before,div.dataTables_scrollBody>table>thead .sorting:after,div.dataTables_scrollBody>table>thead .sorting_asc:after,div.dataTables_scrollBody>table>thead .sorting_desc:after{display:none}div.dataTables_scrollBody>table>tbody tr:first-child th,div.dataTables_scrollBody>table>tbody tr:first-child td{border-top:none}div.dataTables_scrollFoot>.dataTables_scrollFootInner{box-sizing:content-box}div.dataTables_scrollFoot>.dataTables_scrollFootInner>table{margin-top:0 !important;border-top:none}@media screen and (max-width: 767px){div.dataTables_wrapper div.dataTables_length,div.dataTables_wrapper div.dataTables_filter,div.dataTables_wrapper div.dataTables_info,div.dataTables_wrapper div.dataTables_paginate{text-align:center}div.dataTables_wrapper div.dataTables_paginate ul.pagination{justify-content:center !important}}table.dataTable.table-sm>thead>tr>th:not(.sorting_disabled){padding-right:20px}table.dataTable.table-sm .sorting:before,table.dataTable.table-sm .sorting_asc:before,table.dataTable.table-sm .sorting_desc:before{top:5px;right:.85em}table.dataTable.table-