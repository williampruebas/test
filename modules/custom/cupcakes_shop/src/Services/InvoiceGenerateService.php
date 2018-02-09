<?php

namespace Drupal\cupcakes_shop\Services;

use Drupal\node\Entity\Node;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

/**
 * Manage 'InvoiceGenerateService'.
 */
class InvoiceGenerateService {
  
  public function getTable() {
    $_SESSION['cupcakes'];
    $table = [];
    $total = 0;
    
    foreach ($_SESSION['cupcakes'] as $key => $cupcake) {
      $value_cupcakes = $this->getValue($key, count($cupcake));
      $total = $total + $value_cupcakes;
      $table [] = [
        $this->getName($key),
        count($cupcake),
        $value_cupcakes,
      ];
    }
    $table [] = [
      "TOTAL",
      "",
      $total,
    ];
    $_SESSION['table_data'] = $table;
    
    return $table;
  }
  
  public function getName($nid) {
    $node = Node::load($nid);
    if (isset($node)) {
      return $node->getTitle();
    }
    else {
      return "No disponible";
    }
  }
  
  public function getValue($nid, $amount) {
    $node = Node::load($nid);
    if (isset($node)) {
      $discount = 1 - (intval($node->get('field_discount')
            ->getValue()[0]['value']) / 100);
      $value = floatval($node->get('field_value')
          ->getValue()[0]['value']) * $discount;
      return $value * intval($amount);
    }
    else {
      return 0;
    }
  }
  
  public function getFile() {
    $type_file = 'txt';
    $data = $_SESSION['table_data'];
    //creación path del archivo
    $dir = \Drupal::service('stream_wrapper_manager')
      ->getViaUri('public://')
      ->realpath();
    
    $date = date('Y-m-d H:i:s');
    $file_name = 'Factura' . $date . '.' . $type_file;
    $path = $dir . '/' . $file_name;
    
    $data_headers = [
      'Cupcake',
      'Pieces',
      'Value',
    ];
    
    try {
      $file = fopen($path, 'w');
      $header = 'text/plain';
      
      //Write data if export is in format txt or csv
      foreach ($data as $key => $value) {
        foreach ($data_headers as $header => $value_header) {
          fwrite($file, $value_header . "\r\n");
          
          fwrite($file, $data[$key][$header] . "\r\n \r\n");
        }
        fwrite($file, "---------------------------------\r\n \r\n");
      }
      
      if (fclose($file)) {
      }
      else {
      }
      
      $url = substr($path, strpos($path, 'sites'), strlen($path));
      return ['url' => $url, 'path' => $path, 'file_name' => $file_name];
    }
    catch (Exception $exception) {
    }
  }
  
  public function reportByMonth() {
    $data = [];
    $data_headers = [
      'Month',
      'Cupcakes',
      'Pieces',
    ];
    
    $database = \Drupal::database();
    $query = $database->select('sales', 'sales');
    $query->join('node_field_data', 'node', 'node.nid = sales.cupcake');
    $query->addField('sales', 'month');
    $query->addField('node', 'title');
    $query->addField('sales', 'amount');
    $query->orderBy('sales.month', 'ASC');
    
    $result = $query->execute()->fetchAll();
    $aux = [];
    
    
    foreach ($result as $item) {
      $aux [$item->title][] = [
        'month' => $item->month,
        'title' => $item->title,
        'amount' => $item->amount
      ];
    }
    
    foreach ($aux as $value) {
      $row = [];
      if (count($value) > 1) {
        $count = 0;
        foreach ($value as $register) {
          $count = $count + intval($register['amount']);
          $row = [$register['month'], $register['title'], $count];
        }
      }
      else {
        $row = [$value['month'], $value['month'], $value['amount']];
      }
      $data [] = $row;
    }
    
    $file = $this->generateReport($data_headers, $data, 'Month');
    
    return $file;
  }
  
  public function reportByClient() {
    $data = [];
    $data_headers = [
      'client',
      'invoices',
    ];
    
    $database = \Drupal::database();
    $query = $database->select('sales', 'sales');
    $query->join('users_field_data', 'user', 'user.uid = sales.user_id');
    $query->addField('user', 'name');
    $query->addField('sales', 'invoice');
    $query->orderBy('user.name', 'ASC');
    
    $result = $query->execute()->fetchAll();
    $aux = [];
    
    
    foreach ($result as $item) {
      if (!in_array($item->invoice, $aux)) {
        $data [] = ['user' => $item->name, 'invoice' => $item->invoice,];
        $aux[] = $item->invoice;
      }
    }
    
    
    $file = $this->generateReport($data_headers, $data, 'Users');
    
    return $file;
    
  }
  
  public function reportByCupCake(){
    $data = [];
    $data_headers = [
      'Cupcakes',
      'Units',
    ];
  
    $database = \Drupal::database();
    $query = $database->select('sales', 'sales');
    $query->join('node_field_data', 'node', 'node.nid = sales.cupcake');
    $query->addField('node', 'title');
    $query->addField('sales', 'amount');
    $query->orderBy('sales.month', 'ASC');
  
    $result = $query->execute()->fetchAll();
    $aux = [];
  
    
   foreach ($result as $item){
     $aux[$item->title] [] = $item->amount;
   }
    
    foreach ($aux as $key => $value){
      $row = [];
      if(count($value) > 1){
        $count = 0;
        foreach ($value as $register) {
          $count = $count + intval($register['amount']);
          $row = [$key, $count];
        }
      }else{
        $row = [$key, $value[0]];
      }
      $data [] = $row;
    }
    
    
    $file = $this->generateReport($data_headers, $data, 'Rankings');
  
    return $file;
  }
  
  public function generateReport($data_headers, $data, $type) {
    
    //creación path del archivo
    $dir = \Drupal::service('stream_wrapper_manager')
      ->getViaUri('public://')
      ->realpath();
    
    $date = date('Y-m-d H:i:s');
    $file_name = 'Report-' . $type . $date . '.xlsx';
    $path = $dir . '/' . $file_name;
    
    
    try {
      $data_export = $data;
      $type_file = 'xlsx';
      $header = '';
      
      $writer = $type_file == 'xlsx' ? WriterFactory::create(Type::XLSX) : '';
      $writer->openToFile($path);
      
      
      if ($type_file == 'xlsx') {
        $header = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $writer->getCurrentSheet()->setName('Detalle de consumo data');
      }
      
      //Preparación de filas
      
      $group_rows = [];
      
      $writer->addRow($data_headers);
      
      foreach ($data as $item) {
        $writer->addRow($item);
      }
      
      if ($writer->close()) {
      }
      else {
      }
      
      
      $url = substr($path, strpos($path, 'sites'), strlen($path));
      return ['url' => $url, 'path' => $path, 'file_name' => $file_name];
    }
    catch (Exception $exception) {
    }
    
  }
  
  
}
