<?php

use Shuchkin\SimpleXLSX;

function anything_to_utf8 ($var, $deep=TRUE) {
  if (is_array ($var)){
    foreach ($var as $key => $value) {
      if ($deep) {
        $var [$key] = anything_to_utf8 ($value, $deep);
      } elseif (!is_array ($value) && !is_object ($value) && !mb_detect_encoding ($value, 'utf-8', true)){
        $var[$key] = utf8_encode($var);
      }
    }

    return $var;
  } elseif (is_object ($var)) {
    foreach ($var as $key => $value) {
      if ($deep) {
        $var->$key = anything_to_utf8 ($value,$deep);
      } elseif (!is_array ($value) 
        && !is_object ($value) 
        && !mb_detect_encoding ($value, 'utf-8', true)) {
        $var->$key = utf8_encode($var);
      }
    }
    
    return $var;
  } else {
    return (!mb_detect_encoding ($var, 'utf-8', true)) ? utf8_encode ($var) : $var;
  }
}

function lower ($str) {
  return ($str);
}

function import_data_from_excel () {

  $data = [
    'books' => [],
    'authors' => [],
    'editors' => []
  ];

  if ($xlsx = SimpleXLSX::parse (dirname (__DIR__) . '/config/books.xlsx')) {
      
    $rowList = $xlsx->rows ();
    $rows = array_slice ($rowList, 9, count ($rowList));

    $bookKeyListToIgnore = [
      'tombo',
      'disponibilidade',
      'created_at'
    ];

    $bookKeyList = /* $rows [0]
      Array
      (
          [0] => Titulo da Obra
          [1] => Lote
          [2] => Tombo
          [3] => Editora
          [4] => Autor
          [5] => Ano da Publicacao
          [6] => Genero
          [7] => Reg Exemplar
          [8] => Disponbilidade
          [9] => Situação
          [10] => Data de Cadastro
      )

      */ [
      'titulo',
      'lote',
      'tombo',
      'editora',	
      'autor',	
      'ano',
      'genero',	
      'exemplares',
      'disponibilidade',
      'situacao',
      'created_at'
    ];

    $bookList = [];

    for ($i = 1; $i < count ($rows); $i++) {
      $row = $rows [$i];

      $book = [];

      foreach ($row as $index => $value) {
        if (!in_array (lower ($bookKeyList [$index]), $bookKeyListToIgnore)) {
          $key = isset ($bookKeyList [$index]) ? $bookKeyList [$index] : null;

          $book [lower ($key)] = $value;
        }
      }

      array_push ($bookList, $book);
    }

    
    $authorList = [];
    $authors = array_map (function ($book) {
      return $book ['autor'];
    }, $bookList);

    for ($i = 0; $i < count ($authors); $i++) {
      $name = lower ($authors [$i]);

      if (!in_array ($name, $authorList)) {
        array_push ($authorList, $name );
      }
    }

    $editorList = [];
    $editors = array_map (function ($book) {
      return $book ['editora'];
    }, $bookList);

    for ($i = 0; $i < count ($editors); $i++) {
      $name = lower ($editors [$i]);

      if (!in_array ($name, $editorList)) {
        array_push ($editorList, $name );
      }
    }
    
    $data = [
      'books' => $bookList,
      'authors' => array_map (function ($item) { 
        return ['nome' => trim ($item)];
      }, $authorList),
      'editors' => array_map (function ($item) { 
        return ['nome' => trim ($item)];
      }, $editorList)
    ];
  }

  return $data;
}
