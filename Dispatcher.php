<?php
class Dispatcher
{
  public function dispatch()
  {
    echo 'Dispacher Class Call!!<br>';

    $uri_array = $this->replaceUri();   // '/'が最初にある形で代入される

    $class_instance = $this->getControllerInstance($uri_array);
    $class_instance->indexAction(array_shift($uri_array));
  }

  // /hatena_diary以下のURIの配列化. /hatena_diary/hoge/fooの場合、['hoge', 'foo']を返す
  function replaceUri()
  {
    $url = $_SERVER['REQUEST_URI'];   // テスト中はコメントアウトする
    $url = '/hatena_diary/login/hogehoge';    //テスト用

    $pass = parse_url($url, PHP_URL_PATH);
    $index_part = strrchr($pass, '/index');
    $remove_index = str_replace($index_part, '', $pass);
    $remove_hatena_diary = str_replace('/hatena_diary', '', $remove_index);
    $url_normalization = ltrim($remove_hatena_diary, '/');
    return explode('/', $url_normalization);
  }

  //引数の0番目の要素を参考にController系のクラスのインスタンスを返す
  function getControllerInstance($uri_array)
  {
    //URIが存在しない場合は、indexControllerを呼ぶ
    if($uri_array[0] == '')
    {
      return $this->loadAndGetControllerInstance('index');
    }

    switch ($uri_array[0]) 
    {
      case 'login':
        return $this->loadAndGetControllerInstance('login');
      
      case 'logout':
        return $this->loadAndGetControllerInstance('logout');
        break;
    }

    // 数字のみで構成されている場合
    if(ctype_digit($uri_array[0]))
    {
      return $this->loadAndGetControllerInstance('');
    }
    // 英数字のみで構成されている場合
    if(ctype_alnum($uri_array[0]))
    {
      return $this->loadAndGetControllerInstance('');
    }

    //それ以外は不正なURL
    throw new Exception('不正なURLでアクセスされました');
  }

  // 引数の名前がControllerの前についたControllerファイルをロード&インデックスを生成し、インデックスを返すメソッド
  function loadAndGetControllerInstance($controller_first_name)
  {
    $controller_name = ucfirst($controller_first_name) . 'Controller';
    require_once('./controllers/' . $controller_name . '.php');
    return new $controller_name();
  }
}