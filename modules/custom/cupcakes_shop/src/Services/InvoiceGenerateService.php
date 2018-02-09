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
      $discount = 1 - (intval($node->get('field_discount')->getValue()[0]['value'])/100);
      $value = floatval($node->get('field_value')->getValue()[0]['value']) * $discount;
      return $value * intval($amount);
    }
    else {
      return 0;
    }
  }
  
  public function getFile(){
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
  
}
