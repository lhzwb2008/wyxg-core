<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{

    /**
     * 主接口，兼容js的callback调用
     */
    public function call()
    {
        $ip = get_client_ip();
        $callback = I('get.callback', false);
        
        if (S('ip') && $ip == S('ip')) {
            if ($callback) {
                $data = array(
                    'url' => '',
                    'fenxiang' => '',
                    'busy' =>'true'
                );
                echo "callback(" . json_encode($data) . ");";
                return;
            } else {
                $data = array(
                    'url' => '',
                    'fenxiang' => '',
                    'busy' =>'true'
                );
                $this->ajaxReturn($data, 'JSON');
            }
        } else {
            S('ip', $ip, 1);
        }
        $title = I('get.title', '我的歌');
        $content = I('get.content');
        $hasweak = I('get.hasweak', 0);
        $source = I('get.source', rand(1, 6));
        $genre = I('get.genre', rand(0, 3));
        $emotion = I('get.emotion', rand(0, 1));
        $rate = I('get.rate', 0.5);
        $contents = explode(",", $content);
        $name = md5($title . ":" . $content);
        exec('sh ./Public/getname.sh ' . $name . ' 2>&1', $ret, $sta);
        $ret_int = 0;
        if ($ret) {
            $ret_int = (int) $ret[0];
        }
        if ($ret_int > 0) {
            $ret_int = $ret_int + 1;
            $name = $name . "_" . $ret_int;
        } else {
            $name = $name . "_1";
        }
        $url = "";
        $fenxiang = "";
        $size = sizeof($contents);
        if ($size > 16 || $size == 1 || $size == 7 || $size == 11 || $size == 13 || $size == 14 || $size == 15) {
            $data = array(
                'url' => '',
                'fenxiang' => ''
            );
            $this->ajaxReturn($data, 'JSON');
        }
        $zhugesize = $this->get_zhugesize($size);
        $fugesize = $size - $zhugesize;
        $count = array();
        $geci = '';
        $geciList = array();
        for ($i = 0; $i < $size; $i ++) {
            // $newStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $contents[$i]);
            $newStr = $contents[$i];
            $count[$i] = mb_strlen($newStr, "utf-8");
            $geci = $geci . $newStr;
            $geciList[$i] = $newStr;
            if ($i < $zhugesize) { // 进位取整
                $zhugecount = $zhugecount . $count[$i] . ",";
            } else {
                $fugecount = $fugecount . $count[$i] . ",";
            }
        }
        $lrc = $title . ":" . join(",", $geciList);
        $gecicount = 0;
        $tag = 0;
        foreach ($count as $key => $value) {
            if ($value > 13) {
                $tag = 1;
            }
            $gecicount = $gecicount + $value;
        }
        if (strlen($zhugecount) > 0 && strlen($fugecount) > 0) {
            $zhugecount = substr($zhugecount, 0, strlen($zhugecount) - 1);
            $fugecount = substr($fugecount, 0, strlen($fugecount) - 1);
        }
        $velocity = 100 - (int) ($gecicount / $size) * 5 + $rate * 120;
        if ($source == 1 || $source == 2) {
            $gender = 0;
        } else {
            $gender = 1;
        }
        if ($gender == 1) {
            $melody_range_a = "47,61";
            $melody_range_b = "47,65";
        } else {
            $melody_range_a = "38,53";
            $melody_range_b = "38,57";
        }
        exec('sh ./run.sh ' . $name . ' ' . $zhugecount . ' ' . $fugecount . ' ' . $gecicount . ' ' . $geci . ' ' . $velocity . ' "' . $lrc . '" ' . $source . ' ' . $melody_range_a . ' ' . $melody_range_b . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' ' . $hasweak . ' 2>&1', $result, $status);
//         echo 'sh ./run.sh ' . $name . ' ' . $zhugecount . ' ' . $fugecount . ' ' . $gecicount . ' ' . $geci . ' ' . $velocity . ' "' . $lrc . '" ' . $source . ' ' . $melody_range_a . ' ' . $melody_range_b . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' ' . $hasweak . ' 2>&1';
//         print_r($result);
        if ($status == 0 && $tag == 0) {
            $url = 'http://1.117.109.129/core/music/mp3/' . $name . '.mp3';
            $fenxiang = 'http://1.117.109.129/php/home/index/play/' . $name;
        }
        
        if ($callback) {
            $data = array(
                'url' => $url,
                'fenxiang' => $fenxiang
            );
            echo "callback(" . json_encode($data) . ");";
            return;
        } else {
            $data = array(
                'url' => $url,
                'fenxiang' => $fenxiang
            );
            $this->ajaxReturn($data, 'JSON');
        }
    }

    /**
     * 上传mid写歌接口
     */
    public function call_mid()
    {
        $ip = get_client_ip();
        if (S('ip') && $ip == S('ip')) {
            $data = array(
                'url' => '',
                'fenxiang' => ''
            );
            $this->ajaxReturn($data, 'JSON');
        } else {
            S('ip', $ip, 1);
        }
        $title = I('post.title', '我的歌');
        $content = I('post.content');
        $source = I('post.source', rand(0, 6));
        $genre = I('post.genre', rand(0, 3));
        $emotion = I('post.emotion', rand(0, 1));
        $rate = I('post.rate', 0.5);
        $name = I('post.name');
        
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 0;
        $upload->rootPath = './music/mid/'; // 设置附件上传根目录
        $upload->exts = array(
            'mid'
        ); // 设置附件上传类型
        $upload->replace = true;
        $upload->hash = false;
        $upload->autoSub = false;
        $upload->saveName = $name;
        // 上传mid
        $info = $upload->upload();
        
        $url = '';
        $fenxiang = '';
        
        if ($info) {
            $contents = explode(",", $content);
            $size = sizeof($contents);
            if ($size > 16 || $size == 1 || $size == 7 || $size == 11 || $size == 13 || $size == 14 || $size == 15) {
                $data = array(
                    'url' => '',
                    'fenxiang' => ''
                );
                $this->ajaxReturn($data, 'JSON');
            }
            $zhugesize = $this->get_zhugesize($size);
            $fugesize = $size - $zhugesize;
            $count = array();
            $geci = '';
            $geciList = array();
            for ($i = 0; $i < $size; $i ++) {
                // $newStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $contents[$i]);
                $newStr = $contents[$i];
                $count[$i] = mb_strlen($newStr, "utf-8");
                $geci = $geci . $newStr;
                $geciList[$i] = $newStr;
                if ($i < $zhugesize) { // 进位取整
                    $zhugecount = $zhugecount . $count[$i] . ",";
                } else {
                    $fugecount = $fugecount . $count[$i] . ",";
                }
            }
            $lrc = $title . ":" . join(",", $geciList);
            $gecicount = 0;
            $tag = 0;
            foreach ($count as $key => $value) {
                if ($value > 13) {
                    $tag = 1;
                }
                $gecicount = $gecicount + $value;
            }
            if (strlen($zhugecount) > 0 && strlen($fugecount) > 0) {
                $zhugecount = substr($zhugecount, 0, strlen($zhugecount) - 1);
                $fugecount = substr($fugecount, 0, strlen($fugecount) - 1);
            }
            
            $velocity = 100 - (int) ($gecicount / $size) * 5 + $rate * 120;
            if ($source == 1 || $source == 2) {
                $gender = 0;
            } else {
                $gender = 1;
            }
            if ($gender == 1) {
                $melody_range_a = "47,61";
                $melody_range_b = "47,65";
            } else {
                $melody_range_a = "38,53";
                $melody_range_b = "38,57";
            }
            exec('sh ./run_mid.sh ' . $name . ' ' . $gecicount . ' ' . $geci . ' ' . $velocity . ' ' . $lrc . ' ' . $source . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' 2>&1', $result, $status);
            // echo 'sh ./run_mid.sh ' . $name . ' ' . $gecicount . ' ' . $geci . ' ' . $velocity . ' ' . $lrc . ' ' . $source . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' 2>&1';
            // print_r($result);
            if ($status == 0 && $tag == 0) {
                $url = 'http://1.117.109.129/core/music/mp3/' . $name . '.mp3';
                $fenxiang = 'http://1.117.109.129/php/home/index/play/' . $name;
            }
        }
        $data = array(
            'url' => $url,
            'fenxiang' => $fenxiang
        );
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 换伴奏接口
     */
    public function call_acc()
    {
        $name = I('get.name');
        $content = I('get.content');
        $genre = I('get.genre', rand(0, 3));
        $emotion = I('get.emotion', rand(0, 1));
        $rate = I('get.rate', 0.5);
        $contents = explode(",", $content);
        $url = "";
        $fenxiang = "";
        $size = sizeof($contents);
        if ($size > 16 || $size == 1 || $size == 7 || $size == 11 || $size == 13 || $size == 14 || $size == 15) {
            $data = array(
                'url' => '',
                'fenxiang' => ''
            );
            $this->ajaxReturn($data, 'JSON');
        }
        $zhugesize = $this->get_zhugesize($size);
        $fugesize = $size - $zhugesize;
        for ($i = 0; $i < $size; $i ++) {
            $newStr = $contents[$i];
            $count[$i] = mb_strlen($newStr, "utf-8");
            if ($i < $zhugesize) { // 进位取整
                $zhugecount = $zhugecount . $count[$i] . ",";
            } else {
                $fugecount = $fugecount . $count[$i] . ",";
            }
        }
        if (strlen($zhugecount) > 0 && strlen($fugecount) > 0) {
            $zhugecount = substr($zhugecount, 0, strlen($zhugecount) - 1);
            $fugecount = substr($fugecount, 0, strlen($fugecount) - 1);
        }
        $velocity = 100 - (int) ($gecicount / $size) * 5 + $rate * 120;
        exec('sh ./run_acc.sh ' . $name . ' ' . $zhugecount . ' ' . $fugecount . ' ' . $velocity . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' 2>&1', $result, $status);
        // echo 'sh ./run_acc.sh ' . $name . ' ' . $zhugecount . ' ' . $fugecount . ' ' . $velocity . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' 2>&1';
        // print_r($result);
        if ($status == 0) {
            $url = 'http://1.117.109.129/core/music/mp3/' . $name . '.mp3';
            $fenxiang = 'http://1.117.109.129/php/home/index/play/' . $name;
        }
        $data = array(
            'url' => $url,
            'fenxiang' => $fenxiang
        );
        $this->ajaxReturn($data, 'JSON');
    }

    public function change_voice()
    {
        $content = I('get.content');
        $contents = explode(",", $content);
        $name = I('get.name');
        $source = I('get.source');
        $count = array();
        for ($i = 0; $i < sizeof($contents); $i ++) {
            $newStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $contents[$i]);
            $count[$i] = mb_strlen($newStr, "utf-8");
            $geci = $geci . $newStr;
        }
        $gecicount = 0;
        foreach ($count as $key => $value) {
            $gecicount = $gecicount + $value;
        }
        exec('sh ./change_voice.sh ' . $name . ' ' . $geci . ' ' . $gecicount . ' ' . $source . ' 2>&1', $ret, $sta);
        if ($sta == 0) {
            $data = array(
                'mp3' => 'http://1.117.109.129/core/music/mp3/' . $name . '.mp3'
            );
            $this->ajaxReturn($data, 'JSON');
        }
    }

    /**
     * 上传图片
     */
    public function upload_img()
    {
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 0;
        $upload->rootPath = './music/png/'; // 设置附件上传根目录
        $upload->exts = array(
            'png'
        ); // 设置附件上传类型
        $upload->replace = true;
        $upload->hash = false;
        $upload->autoSub = false;
        $upload->saveName = '';
        // 上传mid
        $info = $upload->upload();
        $status = - 1;
        if ($info) {
            $status = 0;
        }
        $data = array(
            'status' => $status
        );
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 上传真人歌曲
     */
    public function upload_song()
    {
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 0;
        $upload->rootPath = './music/mp3/'; // 设置附件上传根目录
        $upload->exts = array(
            'mp3'
        ); // 设置附件上传类型
        $upload->replace = true;
        $upload->hash = false;
        $upload->autoSub = false;
        $upload->saveName = '';
        // 上传mid
        $info = $upload->upload();
$status = - 1;
        if ($info) {
            $status = 0;
        }
        $data = array(
            'status' => $status
        );
        $this->ajaxReturn($data, 'JSON');
    }
    
    /**
     * 拷贝现有歌曲
     */
    public function copy_song()
    {
        $name = I('get.name');
        $newName = '';
        $names = explode("_", $name);
        exec('sh ./Public/getname.sh ' . $names[0] . ' 2>&1', $ret, $sta);
        $ret_int = 0;
        if ($ret) {
            $ret_int = (int) $ret[0];
        }
        if ($ret_int > 0) {
            $ret_int = $ret_int + 1;
            $newName = $names[0] . "_" . $ret_int;
        } else {
            $newName = $names[0] . "_1";
        }
        exec('sh ./copy_song.sh ' . $name . ' ' . $newName . ' 2>&1', $ret, $sta);
        $data = array(
            'name' => $newName
        );
        $this->ajaxReturn($data, 'JSON');
    }
    
    

    /**
     * 安卓更新接口
     */
    public function android_update()
    {
        $build = (int) I('get.build', 0);
        exec('ls -t ./Public/apk|head -n 1', $result, $status);
        $newestBuild = 0;
        $apk = '';
        if ($result) {
            $apk = $result[0];
            $apks = explode(".", $apk);
            $newestBuild = $apks[0];
        }
        if ($newestBuild > $build) {
            $data['apk'] = 'http://1.117.109.129/core/core/Public/apk/' . $apk;
            $this->ajaxReturn($data);
        } else {
            $data['apk'] = '';
            $this->ajaxReturn($data);
        }
    }

    function get_zhugesize($num)
    {
        if ($num == 1) {
            return 1;
        } else 
            if ($num == 2) {
                return 1;
            } else 
                if ($num == 3) {
                    return 2;
                } else 
                    if ($num == 4) {
                        return 2;
                    } else 
                        if ($num == 5) {
                            return 4;
                        } else 
                            if ($num == 6) {
                                return 4;
                            } else 
                                if ($num == 8) {
                                    return 4;
                                } else 
                                    if ($num == 9) {
                                        return 8;
                                    } else 
                                        if ($num == 10) {
                                            return 8;
                                        } else 
                                            if ($num == 12) {
                                                return 8;
                                            } else 
                                                if ($num == 16) {
                                                    return 8;
                                                } else {
                                                    return 1;
                                                }
    }

    function validateCode($validate_code)
    {
        $file = fopen("http://1.117.109.129/core/music/regcode", "r");
        $success = 0;
        while (! feof($file)) {
            $code = fgets($file);
            if ((int) $code == (int) $validate_code) {
                $success = 1;
            }
        }
        fclose($file);
        if ($success == 1) {
            echo "success";
        } else {
            echo "fail";
        }
    }

    /**
     * H5接口
     */
    public function call_h5()
    {
        header("Access-Control-Allow-Origin:*");
        $ip = get_client_ip();
        
        if (S('ip') && $ip == S('ip')) {
            $data = array(
                'url' => ''
            );
            echo "callback(" . json_encode($data) . ");";
            return;
        } else {
            S('ip', $ip, 1);
        }
        $content = I('get.content');
        $user_name = I('get.name');
        $age = I('get.age');
        $num = I('get.num');
        $randnum = array(
            2,
            6
        );
        $i = rand(0, 1);
        $source = $randnum[$i];
        $genre = rand(0, 3);
        $emotion = rand(0, 1);
        $rate = rand(5, 8) / 10;
        $name = md5($content);
        $url = "";
        $img = "";
        $contents = $this->separate($content);
        $size = sizeof($contents);
        if ($size > 16) {
            $data = array(
                'url' => ''
            );
            echo "callback(" . json_encode($data) . ");";
            return;
        }
        exec('sh ./Public/getname.sh ' . $name . ' 2>&1', $ret, $sta);
        $ret_int = 0;
        if ($ret) {
            $ret_int = (int) $ret[0];
        }
        if ($ret_int > 0) {
            $ret_int = $ret_int + 1;
            $name = $name . "_" . $ret_int;
        } else {
            $name = $name . "_1";
        }
        
        $this->getImg($user_name,$age,$num,$name);
        
        $zhugesize = $this->get_zhugesize($size);
        $fugesize = $size - $zhugesize;
        $count = array();
        $geci = '';
        for ($i = 0; $i < $size; $i ++) {
            $newStr = $contents[$i];
            $count[$i] = mb_strlen($newStr, "utf-8");
            $geci = $geci . $newStr;
            if ($i < $zhugesize) { // 进位取整
                $zhugecount = $zhugecount . $count[$i] . ",";
            } else {
                $fugecount = $fugecount . $count[$i] . ",";
            }
        }
        $lrc = $content;
        $gecicount = 0;
        $tag = 0;
        foreach ($count as $key => $value) {
            $gecicount = $gecicount + $value;
        }
        if (strlen($zhugecount) > 0 && strlen($fugecount) > 0) {
            $zhugecount = substr($zhugecount, 0, strlen($zhugecount) - 1);
            $fugecount = substr($fugecount, 0, strlen($fugecount) - 1);
        }
        $velocity = 100 - (int) ($gecicount / $size) * 5 + $rate * 120;
        if ($source == 1 || $source == 2) {
            $gender = 0;
        } else {
            $gender = 1;
        }
        if ($gender == 1) {
            $melody_range_a = "47,61";
            $melody_range_b = "47,65";
        } else {
            $melody_range_a = "38,53";
            $melody_range_b = "38,57";
        }
        exec('sh ./run_h5.sh ' . $name . ' ' . $zhugecount . ' ' . $fugecount . ' ' . $gecicount . ' ' . $geci . ' ' . $velocity . ' ' . $lrc . ' ' . $source . ' ' . $melody_range_a . ' ' . $melody_range_b . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' 2>&1', $result, $status);
//         echo 'sh ./run_h5.sh ' . $name . ' ' . $zhugecount . ' ' . $fugecount . ' ' . $gecicount . ' ' . $geci . ' ' . $velocity . ' ' . $lrc . ' ' . $source . ' ' . $melody_range_a . ' ' . $melody_range_b . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' 2>&1';
//         print_r($result);
        if ($status == 0) {
            $url = 'http://1.117.109.129/core/music/mp3/' . $name . '.mp3';
            $img = 'http://1.117.109.129/core/music/activity_img/' . $name . '.jpg';
        }
        $data = array(
            'url' => $url,
            'img' => $img
        );
        
        echo "callback(" . json_encode($data) . ");";
    }
    
    
    public function call_h5_1()
    {
        header("Access-Control-Allow-Origin:*");
        $ip = get_client_ip();
    
        if (S('ip') && $ip == S('ip')) {
            $data = array(
                'url' => ''
            );
            echo "callback(" . json_encode($data) . ");";
            return;
        } else {
            S('ip', $ip, 1);
        }
        $content = I('post.content');
        $content_raw = I('post.content_raw');
        $randnum = array(
            2,
            6
        );
        $i = rand(0, 1);
        $source = $randnum[$i];
        $genre = rand(0, 3);
        $emotion = rand(0, 1);
        $rate = rand(5, 8) / 10;
        $name = md5($content);
        
        $url = "";
        $img = "";
        $contents = $this->separate($content);
        $size = sizeof($contents);
        if ($size > 16) {
            $data = array(
                'url' => ''
            );
            echo "callback(" . json_encode($data) . ");";
            return;
        }
        exec('sh ./Public/getname.sh ' . $name . ' 2>&1', $ret, $sta);
        $ret_int = 0;
        if ($ret) {
            $ret_int = (int) $ret[0];
        }
        if ($ret_int > 0) {
            $ret_int = $ret_int + 1;
            $name = $name . "_" . $ret_int;
        } else {
            $name = $name . "_1";
        }
    
        $zhugesize = $this->get_zhugesize($size);
        $fugesize = $size - $zhugesize;
        $count = array();
        $geci = '';
        for ($i = 0; $i < $size; $i ++) {
            $newStr = $contents[$i];
            $count[$i] = mb_strlen($newStr, "utf-8");
            $geci = $geci . $newStr;
            if ($i < $zhugesize) { // 进位取整
                $zhugecount = $zhugecount . $count[$i] . ",";
            } else {
                $fugecount = $fugecount . $count[$i] . ",";
            }
        }
        $lrc = $content_raw;
        $gecicount = 0;
        $tag = 0;
        foreach ($count as $key => $value) {
            $gecicount = $gecicount + $value;
        }
        if (strlen($zhugecount) > 0 && strlen($fugecount) > 0) {
            $zhugecount = substr($zhugecount, 0, strlen($zhugecount) - 1);
            $fugecount = substr($fugecount, 0, strlen($fugecount) - 1);
        }
        $velocity = 100 - (int) ($gecicount / $size) * 5 + $rate * 120;
        if ($source == 1 || $source == 2) {
            $gender = 0;
        } else {
            $gender = 1;
        }
        if ($gender == 1) {
            $melody_range_a = "47,61";
            $melody_range_b = "47,65";
        } else {
            $melody_range_a = "38,53";
            $melody_range_b = "38,57";
        }
        exec('sh ./run_h5.sh ' . $name . ' ' . $zhugecount . ' ' . $fugecount . ' ' . $gecicount . ' ' . $geci . ' ' . $velocity . ' ' . $lrc . ' ' . $source . ' ' . $melody_range_a . ' ' . $melody_range_b . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' 2>&1', $result, $status);
        //         echo 'sh ./run_h5.sh ' . $name . ' ' . $zhugecount . ' ' . $fugecount . ' ' . $gecicount . ' ' . $geci . ' ' . $velocity . ' ' . $lrc . ' ' . $source . ' ' . $melody_range_a . ' ' . $melody_range_b . ' ' . $genre . ' ' . $emotion . ' ' . $zhugesize . ' ' . $fugesize . ' 2>&1';
        //         print_r($result);
        if ($status == 0) {
            $url = 'http://1.117.109.129/core/music/mp3/' . $name . '.mp3';
        }
        $data = array(
            'url' => $url
        );
    
        echo "callback(" . json_encode($data) . ");";
    }
    

    function separate($content)
    {
        $contents = explode(",", $content);
        $size = sizeof($contents);
        if ($size == 1) {
            $content = $content . "," . $content;
            $contents = explode(",", $content);
            $size = sizeof($contents);
        }
        $result = array();
        for ($i = 0; $i < $size; $i ++) {
            $str = $contents[$i];
            $count = mb_strlen($str, "utf-8");
            if ($i + 1 < $size) {
                $strnext = $contents[$i + 1];
                $countnext = mb_strlen($strnext, "utf-8");
                // 合短句
                if ($size > 4 && $countnext < 6 && $count < 6) {
                    array_push($result, $str . $strnext);
                    $i = $i + 1;
                    continue;
                }
            } 
            if ($count == 1) {
                $str = $str . $str;
            }
            // 拆长句
            $this->depart($result, $count, $str);
        }
        
        $newsize = sizeof($result);
        while ($newsize == 7 || $newsize == 11 || $newsize == 13 || $newsize == 14 || $newsize == 15) {
            $maxcount = 0;
            $maxi = 0;
            for ($i = 0; $i < $newsize; $i ++) {
                $str = $result[$i];
                $count = mb_strlen($str, "utf-8");
                if ($count > $maxcount) {
                    $maxcount = $count;
                    $maxi = $i;
                }
            }
            
            $maxstr = $result[$maxi];
            $str1 = mb_substr($maxstr, 0, (int) ($maxcount / 2), "utf-8");
            $str2 = mb_substr($maxstr, (int) ($maxcount / 2), (int) ($maxcount / 2) + 1, "utf-8");
            for ($i = $newsize; $i > $maxi + 1; $i --) {
                $result[$i] = $result[$i - 1];
            }
            $result[$maxi] = $str1;
            $result[$maxi + 1] = $str2;
            $newsize = sizeof($result);
        }
        return $result;
    }

    function depart(&$result, $count, $str)
    {
        if ($count <= 13) {
            array_push($result, $str);
            return;
        }
        $str1 = mb_substr($str, 0, (int) ($count / 2), "utf-8");
        $this->depart($result, (int) ($count / 2) + 1, $str1);
        $str2 = mb_substr($str, (int) ($count / 2), $count - 1, "utf-8");
        $this->depart($result, (int) ($count / 2), $str2);
    }
    
    function getImg($user_name,$age,$num,$name){
        $bg='./Public/images/'.$num.'.jpg';
        $img = imagecreatefromjpeg($bg);  
        $savename='./music/activity_img/'.$name.'.jpg';
        //生成文字
        Imagefttext($img, 25, 0, 582, 115, imagecolorallocate($img,255,91,0), "./Public/font/font.ttf", $user_name);
        Imagefttext($img, 25, 0, 578, 160, imagecolorallocate($img,255,91,0), "./Public/font/font.ttf", $age);
        imagejpeg($img,$savename);
        imagedestroy($img);
        $this->erweima($name);
        $image = new \Think\Image();
        $image->open($savename)->water('./music/activity_img/'.$name.'_erweima.png',\Think\Image::IMAGE_WATER_SOUTHWEST)->save($savename);
    }
    
    function erweima($name){
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval(3) ;//容错级别
        $matrixPointSize = intval(3);//生成图片大小
        //生成二维码图片
        $object = new \QRcode();
        $filename ='./music/activity_img/'.$name.'_erweima.png'; //图片输出路径和文件名
        $url = 'http://1.117.109.129/php/home/index/qxActive/'.$name;
        $object->png($url, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        
    }
    
    public function zouyin(){
        $yuanqu = I('get.yuanqu');
        if($yuanqu=='qcxlsc'|$yuanqu=='zdsjdjt'){
            $source=1;
        }else{
            $source=6;
        }
        $title = I('get.title');
        $content = I('get.content');
        $contents = explode(",", $content);
        $name = md5($title . ":" . $content);
        exec('sh ./Public/getname.sh ' . $name . ' 2>&1', $ret, $sta);
        $ret_int = 0;
        if ($ret) {
            $ret_int = (int) $ret[0];
        }
        if ($ret_int > 0) {
            $ret_int = $ret_int + 1;
            $name = $name . "_" . $ret_int;
        } else {
            $name = $name . "_1";
        }
        $count = array();
        $geci = '';
        $geciList = array();
        $size = sizeof($contents);
        for ($i = 0; $i < $size; $i ++) {
            $newStr = $contents[$i];
            $count[$i] = mb_strlen($newStr, "utf-8");
            $geci = $geci . $newStr;
            $geciList[$i] = $newStr;
        }
        $lrc = $title . ":" . join(",", $geciList);
        $gecicount = 0;
        foreach ($count as $key => $value) {
            $gecicount = $gecicount + $value;
        }
        $acc_file = '/data/music/zouyin_acc/'.$yuanqu.'.wav';
        $mid_file = '/data/music/zouyin_mid/'.$yuanqu.'.mid';
        
        exec('sh ./zouyin.sh ' . $acc_file . ' ' . $mid_file . ' ' . $geci. ' ' . $gecicount . ' ' . $name .' ' . $lrc.' ' . $source.' 2>&1', $result, $status);
//         print_r($result);
        $url = 'http://1.117.109.129/core/music/mp3/' . $name . '.mp3';
        $fenxiang = 'http://1.117.109.129/core/home/index/play/' . $name;
        $data = array(
            'url' => $url,
            'fenxiang' => $fenxiang
        );
        $this->ajaxReturn($data, 'JSON');
    }
    
    public function template(){
        $id = I('post.id');
        $source = I('post.singer');
        $title = I('post.title');
        $content = I('post.content');
        $content = trim($content);
        $contents = explode(",", $content);
        
        $name = md5($title . ":" . $content);
        exec('sh ./Public/getname.sh ' . $name . ' 2>&1', $ret, $sta);
        $ret_int = 0;
        if ($ret) {
            $ret_int = (int) $ret[0];
        }
        if ($ret_int > 0) {
            $ret_int = $ret_int + 1;
            $name = $name . "_" . $ret_int;
        } else {
            $name = $name . "_1";
        }
        $count = array();
        $geci = '';
        $geciList = array();
        $size = sizeof($contents);
        for ($i = 0; $i < $size; $i ++) {
            $newStr = $contents[$i];
            $count[$i] = mb_strlen($newStr, "utf-8");
            $geci = $geci . $newStr;
            $geciList[$i] = $newStr;
        }
        $lrc = $title . ":" . join(",", $geciList);
        $gecicount = 0;
        foreach ($count as $key => $value) {
            $gecicount = $gecicount + $value;
        }
        $acc_file = '/data/music/zouyin/'.$id.'.wav';
        $mid_file = '/data/music/zouyin/'.$id.'.mid';
    
        exec('sh ./zouyin.sh ' . $acc_file . ' ' . $mid_file . ' ' . $geci. ' ' . $gecicount . ' ' . $name .' ' . $lrc.' ' . $source.' 2>&1', $result, $status);
//         echo 'sh ./zouyin.sh ' . $acc_file . ' ' . $mid_file . ' ' . $geci. ' ' . $gecicount . ' ' . $name .' ' . $lrc.' ' . $source.' 2>&1';
//         print_r($result); 
        $url = 'http://1.117.109.129/core/music/mp3/' . $name . '.mp3';
        $fenxiang = 'http://1.117.109.129/php/home/index/play/' . $name;
        $data = array(
            'url' => $url,
            'fenxiang' => $fenxiang
        );
        $this->ajaxReturn($data, 'JSON');
    }
    
}
