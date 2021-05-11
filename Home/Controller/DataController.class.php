<?php
namespace Home\Controller;

use Think\Controller;

class DataController extends Controller
{
    const TESTHEAD="http://123.59.134.79";
    const ONLINEHEAD="http://1.117.109.129/core";
    public function addUser()
    {
        
        $User = M("User");
        $data['name'] = I('get.name');
        $data['password'] = I('get.password');
        $data['phone'] = I('get.phone');
        $data['gender'] = I('get.gender');
        $data['birthday'] = I('get.birthday');
        $data['signature'] = I('get.signature');
        $data['weibo'] = I('get.weibo');
        $data['qq'] = I('get.qq');
        $data['wechat'] = I('get.wechat');
        $data['location'] = I('get.location');
        $data['school_company'] = I('get.school_company');
        $data['robot'] = 0;
        if(!empty($data['password'])&&!empty($data['phone'])){
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $data['modify_time'] = date("Y-m-d H:i:s",time());
            $id = $User->data($data)->add();
            if($id==false){//插入失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//插入成功
                $this->ajaxReturn(array('status'=>0), 'JSON');
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    
    public function addRobotUser()
    {
        header("Access-Control-Allow-Origin:*");
        $User = M("User");
        $data['name'] = I('get.name');
        $data['password'] = '000';
        $data['phone'] = I('get.phone');
        $data['gender'] = I('get.gender');
        $data['robot'] = 1;
        if(!empty($data['phone'])){
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $data['modify_time'] = date("Y-m-d H:i:s",time());
            $id = $User->data($data)->add();
            if($id==false){//插入失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//插入成功
                $this->ajaxReturn(array('status'=>0), 'JSON');
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    
    public function getUser()
    {
        header("Access-Control-Allow-Origin:*");
        $User = M("User");
        $data['phone'] = I('get.phone');
        $user = $User->where($data)->find();
        if($user===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($user)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回 
            $user['status']=0;
            $this->ajaxReturn($user, 'JSON');
        }
    }
    
    public function getUserById()
    {
        $User = M("User");
        $data['id'] = I('get.id');
        $user = $User->where($data)->find();
        if($user===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($user)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $user['status']=0;
            $this->ajaxReturn($user, 'JSON');
        }
    }
    
    public function updateUser()
    {
    
        $User = M("User");
        if(I('get.phone')!=null){
            $query['phone'] = I('get.phone');
        }else{
            $this->ajaxReturn(array('status'=>1), 'JSON');
        }
        if(I('get.name')!=null){
            $data['name'] = I('get.name');
        }
        if(I('get.gender')!=null){
            $data['gender'] = I('get.gender');
        }
        if(I('get.password')!=null){
            $data['password'] = I('get.password');
        }
        if(I('get.birthday')!=null){
            $data['birthday'] = I('get.birthday');
        }
        if(I('get.signature')!=null){
            $data['signature'] = I('get.signature');
        }
        if(I('get.weibo')!=null){
            $data['weibo'] = I('get.weibo');
        }
        if(I('get.qq')!=null){
            $data['qq'] = I('get.qq');
        }
        if(I('get.wechat')!=null){
            $data['wechat'] = I('get.wechat');
        }
        if(I('get.location')!=null){
            $data['location'] = I('get.location');
        }
        if(I('get.school_company')!=null){
            $data['school_company'] = I('get.school_company');
        }
        $data['modify_time'] = date("Y-m-d H:i:s",time());
        //更新失败
        if($User->where($query)->save($data)==false){
            $this->ajaxReturn(array('status'=>1), 'JSON');
        }else{//更新成功
            $this->ajaxReturn(array('status'=>0), 'JSON');
        }
    }
    
    /**
     * 上传用户图片
     */
    public function uploadUserImg()
    {
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 0;
        $upload->rootPath = './music/userPng/'; // 设置附件上传根目录
        $upload->exts = array(
            'png'
        ); // 设置附件上传类型
        $upload->replace = true;
        $upload->hash = false;
        $upload->autoSub = false;
        $upload->saveName = '';
        $info = $upload->upload();
        $status = -1;
        if($info){
            $status = 0;
        }
        $data = array(
            'status' => $status
        );
        $this->ajaxReturn($data, 'JSON');
    }
    
    
    public function uploadRobotUserImg()
    {
        header("Access-Control-Allow-Origin:*");
        $phone = I('post.phone');
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 0;
        $upload->rootPath = './music/userPng/'; // 设置附件上传根目录
        $upload->exts = array(
            'png'
        ); // 设置附件上传类型
        $upload->replace = true;
        $upload->hash = false;
        $upload->autoSub = false;
        $upload->saveName = md5($phone);
        $info = $upload->upload();
        $status = -1;
        if($info){
            $status = 0;
        }
        $data = array(
            'status' => $status
        );
        $this->ajaxReturn($data, 'JSON');
    }
    
    public function addSong()
    {
    
        $Song = M("Song");
        $data['tag'] = I('get.tag');
        $data['user_id'] = I('get.user_id');
        $data['device'] = I('get.device');
        $data['code'] = I('get.code');
        $data['public'] = I('get.public');
        $data['sing'] = I('get.sing',0);
        $data['gai'] = I('get.gai',0);
        $data['zuoci_id'] = I('get.zuoci_id',0);
        $data['zuoqu_id'] = I('get.zuoqu_id',0);
        $data['template_id'] = I('get.template_id',0);
        $data['yanchang_id'] = I('get.yanchang_id',0);
        $data['original_title'] = I('get.original_title');
        $data['activity_id'] = I('get.activity_id',0);
        
        $lrcUrl = 'http://1.117.109.129/core/music/lrc/'.$data['code'].'.lrc';
		$lrc = file_get_contents($lrcUrl);
		$contents = explode(":",$lrc);
		$data['title'] = $contents[0];
        $data['is_recommended'] = 0;
        
        $User = M("User");
        $data['user_name'] = $User->where("id='%s'",$data['user_id'])->getField('name');
        if(!empty($data['user_id'])&&!empty($data['code'])){
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $data['modify_time'] = date("Y-m-d H:i:s",time());
            $id = $Song->data($data)->add();
            if($id===false){//插入失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//插入成功
                $this->ajaxReturn(array('status'=>0), 'JSON');
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    
    public function addPlay()
    {
        $Song = M("Song");
        $code = I('get.code');
        $count = rand(1,5);
        if($Song-> where("code='%s'",$code)->setInc('play_count',$count)==false){
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//更新成功
            $this->ajaxReturn(array('status'=>0), 'JSON');
        }       
    }
    
    public function getSongsByUserId()
    {
        $Song = M("Song");
        $data['user_id'] = I('get.user_id');
        $songs = $Song->where($data)->order('create_time desc')->select();
        if($songs===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['songs']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    

    
    /**
     * 根据code增加点赞
     */
    public function upByCode()
    {
        $Song = M("Song");
        $code = I('get.code');
        if($Song-> where("code='%s'",$code)->setInc('up_count')==false){
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//更新成功
            $this->ajaxReturn(array('status'=>0), 'JSON');
        }
    }
    
    /**
     * 根据code增加投诉数
     */
    public function setCheatByCode()
    {
        $Song = M("Song");
        $code = I('get.code');
        if($Song-> where("code='%s'",$code)->setInc('cheat_count')==false){
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//更新成功
            $this->ajaxReturn(array('status'=>0), 'JSON');
        }
    }
    
  
    
    public function delByCode()
    {
        $Song = M("Song");
        $code = I('get.code');
        if($Song-> where("code='%s'",$code)->delete()==false){
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//删除成功
            $this->ajaxReturn(array('status'=>0), 'JSON');
        }
    }
    
    
    public function getTuijian()
    {
        $Song = M("Song");
        $start = I('get.start');
        $length = I('get.length');
        $songs = $Song->where('is_recommended=1 AND public=0 AND sing=0')->order('modify_time desc')->limit($start,$length)->select();
        if($songs===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['songs']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getSingTuijian()
    {
        $Song = M("Song");
        $start = I('get.start');
        $length = I('get.length');
        $songs = $Song->where('is_recommended=1 AND public=0 AND sing=1')->order('modify_time desc')->limit($start,$length)->select();
        if($songs===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['songs']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getZuixin()
    {
        $Song = M("Song");
        $start = I('get.start');
        $length = I('get.length');
        $songs = $Song->where('public=0')->order('create_time desc')->limit($start,$length)->select();
        if($songs===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['songs']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getZuixinGai()
    {
        $Song = M("Song");
        $start = I('get.start');
        $length = I('get.length');
        $songs = $Song->where('public=0 AND gai=1')->order('create_time desc')->limit($start,$length)->select();
        if($songs===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['songs']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getZuixinChang()
    {
        $Song = M("Song");
        $start = I('get.start');
        $length = I('get.length');
        $songs = $Song->where('public=0 AND sing=1')->order('create_time desc')->limit($start,$length)->select();
        if($songs===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['songs']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    
    
    public function getZhoubang()
    {
        $Song = M("Song");
        $start = I('get.start');
        $length = I('get.length');
        $week = date('Y-m-d H:i:s',strtotime('-1 week'));
        $map['public'] = array('eq',0);
        $map['create_time'] = array('gt',$week);
        $songs = $Song->where($map)->order('play_count desc')->limit($start,$length)->select();
        if($songs===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['songs']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function addComments()
    {
    
        $Comments = M("Comments");
        $data['user_id'] = I('get.user_id');
        $data['song_id'] = I('get.song_id');
        $data['content'] = I('get.content');
        $data['parent'] = I('get.parent');
        
        if(!empty($data['user_id'])&&!empty($data['song_id'])&&!empty($data['content'])){
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $User = M('User');
            $data['user_name'] = $User->where("id='%s'",$data['user_id'])->getField('name');
            $id = $Comments->data($data)->add();
            if($id==false){//插入失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//插入成功
                $this->ajaxReturn(array('status'=>0), 'JSON');
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    
    public function delComments()
    {
        $Comments = M("Comments");
        $data['id'] = I('get.id');
        $id = $Comments->where($data)->delete();
        if($id==false){//删除失败
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else{//删除成功
            $this->ajaxReturn(array('status'=>0), 'JSON');
        }
    }
    
    public function getSongByCode()
    {
        $Song = M("Song");
        $code = I('get.code');
        $song = $Song-> where("code='%s'",$code)->find();
        if($song===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($song)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $this->ajaxReturn($song, 'JSON');
        }
    }
    
    public function getSongById()
    {
        $Song = M("Song");
        $id = I('get.id');
        $song = $Song-> where("id='%d'",$id)->find();
        if($song===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($song)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $this->ajaxReturn($song, 'JSON');
        }
    }
    
    public function getCommentsBySongId()
    {
        $Comments = M("Comments");
        $data['song_id'] = I('get.song_id');
        $comments = $Comments->where($data)->order('up_count desc,create_time desc')->select();
        if($comments===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($comments)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            foreach ($comments as &$comment) {
                $User = M("User");
                $comment['user_name'] = $User->where("id='%s'",$comment['user_id'])->getField('name');
                $comment['phone'] = $User->where("id='%s'",$comment['user_id'])->getField('phone');
            }
            $ret['status']=0;
            $ret['comments']=$comments;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function addFocus()
    {
    
        $Focus = M("Focus");
        $data['focus_id'] = I('get.focus_id');
        $data['follow_id'] = I('get.follow_id');
    
        if(!empty($data['focus_id'])&&!empty($data['follow_id'])){
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $id = $Focus->data($data)->add();
            if($id==false){//插入失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//插入成功
                $this->ajaxReturn(array('status'=>0), 'JSON');
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    } 
    
    public function getFocusByfocusId()
    {
        $Focus = M("Focus");
        $data['focus_id'] = I('get.focus_id');
        $focus = $Focus->where($data)->order('create_time desc')->select();
        if($focus===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($focus)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['items']=$focus;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getFocusByfollowId()
    {
        $Focus = M("Focus");
        $data['follow_id'] = I('get.follow_id');     
        $focus = $Focus->where($data)->order('create_time desc')->select();
        if($focus===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($focus)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['items']=$focus;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function delFocus()
    {
    
        $Focus = M("Focus");
        $data['focus_id'] = I('get.focus_id');
        $data['follow_id'] = I('get.follow_id');
    
        if(!empty($data['focus_id'])&&!empty($data['follow_id'])){
            $id = $Focus->where($data)->delete();
            if($id==false){//删除失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//删除成功
                $this->ajaxReturn(array('status'=>0), 'JSON');
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    
    public function addLike()
    {
    
        $Like = M("Like");
        $data['user_id'] = I('get.user_id');
        $data['song_id'] = I('get.song_id');
    
        if(!empty($data['user_id'])&&!empty($data['song_id'])){
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $id = $Like->data($data)->add();
            if($id==false){//插入失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//插入成功
                $Song = M("Song");
                $Song-> where("id=".$data['song_id'])->setInc('up_count');
                $this->ajaxReturn(array('status'=>0), 'JSON');
               
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    
    public function delLike()
    {
    
        $Like = M("Like");
        $data['user_id'] = I('get.user_id');
        $data['song_id'] = I('get.song_id');
    
        if(!empty($data['user_id'])&&!empty($data['song_id'])){
            $id = $Like->where($data)->delete();
            if($id==false){//删除失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//删除成功
                $Song = M("Song");
                $Song-> where("id=".$data['song_id'])->setDec('up_count');
                $this->ajaxReturn(array('status'=>0), 'JSON');
                
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    
    public function getLikeByuserId()
    {
        $Like = M("Like");
        $data['user_id'] = I('get.user_id');
        $like = $Like->where($data)->order('create_time desc')->select();
        if($like===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($like)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            foreach($like as &$l){
                $Song = M("Song");
                $song = $Song-> where("id='%d'",$l["song_id"])->find();
                $l["code"] = $song["code"];
                $l["make_user_id"] = $song["user_id"];
                $l["play_count"] = $song["play_count"];
                $l["up_count"] = $song["up_count"];
                $l["cheat_count"] = $song["cheat_count"];
                $l["is_recommended"] = $song["is_recommended"];
                $l["create_time"] = $song["create_time"];
                $l["modify_time"] = $song["modify_time"];
            }
            $ret['status']=0;
            $ret['items']=$like;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function addMessage()
    {
    
        $Message = M("Message");
        $data['type'] = I('get.type');
        $data['receive_id'] = I('get.receive_id');
        $data['send_id'] = I('get.send_id');
        $data['song_id'] = I('get.song_id');
        $data['content'] = I('get.content');
        $data['song_title'] = I('get.song_title');
        $data['is_read'] = 0;
    
        if($data['type']!=null&&$data['type']!=''&&!empty($data['receive_id'])){
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $data['modify_time'] = date("Y-m-d H:i:s",time());
            $id = $Message->data($data)->add();
            if($id==false){//插入失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//插入成功
                $this->ajaxReturn(array('status'=>0), 'JSON');                 
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    
    public function getMessageByReceiveId()
    {
        $Message = M("Message");
        $data['receive_id'] = I('get.receive_id');
        $message = $Message->where($data)->order('create_time desc')->select();
        if($message===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($message)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['items']=$message;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function delMessage()
    {
    
        $Message = M("Message");
        $data['id'] = I('get.id');
        if(!empty($data['id'])){
            $id = $Message->where($data)->delete();
            if($id==false){//删除失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//删除成功
                $this->ajaxReturn(array('status'=>0), 'JSON');
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    

    public function setReadById()
    {
        $Message = M("Message");
        $id = I('get.id');
        if($Message-> where("id='%d'",$id)->setField('is_read','1')==false){
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//更新成功
            $this->ajaxReturn(array('status'=>0), 'JSON');
        }
    }
    
    public function getRecommendUser()
    {
        $User = M("User");
        $map['recommend']  = array('gt',0);
        $user = $User->where($map)->order('recommend desc,modify_time desc')->select();
        if($user===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($user)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回 
            $ret['status']=0;
            $ret['items']=$user;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    

    
    public function getActivityById()
    {
        $Activity = M("Activity");
        $id = I('get.id');
        $activity = $Activity-> where("id='%d'",$id)->find();
        if($activity===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($activity)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $this->ajaxReturn($activity, 'JSON');
        }
    }
    
    public function getAllActivities()
    {
        $Activity = M("Activity");
        $activity = $Activity-> where("status=1")->order('modify_time desc')->select();
        if($activity===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($activity)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['items']=$activity;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getActivitySong()
    {
        $Song = M("Song");
        $start = I('get.start',0);
        $length = I('get.length',100);
        $activity_id = I('get.activity_id');
        $songs = $Song->where("activity_id='%d'",$activity_id)->limit($start,$length)->order('modify_time desc')->select();
        if($songs===false){
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{
            $ret['status']=0;
            $ret['items']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function addActivity()
    {
    
        $Activity = M("Activity");
        $data['name'] = I('post.name');
        $data['content'] = I('post.content');
        $data['create_time'] = date("Y-m-d H:i:s",time());
        $data['modify_time'] = date("Y-m-d H:i:s",time());
        $data['status'] = 0;
        $id = $Activity->data($data)->add();
        $status = -1;
        if($id==false){//插入失败
            return;
        }else{//插入成功
            $upload = new \Think\Upload(); // 实例化上传类
            $upload->maxSize = 0;
            $upload->rootPath = './music/activityPng/'; // 设置附件上传根目录
            $upload->exts = array(
                'png'
            ); // 设置附件上传类型
            $upload->replace = true;
            $upload->hash = false;
            $upload->autoSub = false;
            $upload->saveName = $id;
            $info = $upload->upload();
            if($info){
                $Activity-> where("id=".$id)->setField('img','http://1.117.109.129/core/music/activityPng/'.$id.'.png');
                $status = 0;
                redirect('http://1.117.109.129/php/home/admin/getActivitySong?activity_id='.$id.'&status='.$status, 1, '页面跳转中...');
            }
        }
    }
    
    
    public function addBanner()
    {
    
        $Banner = M("Banner");
        $data['url'] = I('post.url');
        $data['create_time'] = date("Y-m-d H:i:s",time());
        $data['modify_time'] = date("Y-m-d H:i:s",time());
        $id = $Banner->data($data)->add();
        $status = -1;
        if($id==false){//插入失败
            $status = -2;
        }else{//插入成功
            $upload = new \Think\Upload(); // 实例化上传类
            $upload->maxSize = 0;
            $upload->rootPath = './music/bannerPng/'; // 设置附件上传根目录
            $upload->exts = array(
                'png'
            ); // 设置附件上传类型
            $upload->replace = true;
            $upload->hash = false;
            $upload->autoSub = false;
            $upload->saveName = $id;
            $info = $upload->upload();
            if($info){
                $Banner-> where("id=".$id)->setField('img','http://1.117.109.129/core/music/bannerPng/'.$id.'.png');
                $status = 0;
                redirect('http://1.117.109.129/php/home/admin/getAllBanners?status='.$status, 1, '页面跳转中...');
            }
        }
        //数据不符合规范
        redirect('http://1.117.109.129/php/home/admin/getAllBanners?status='.$status, 1, '页面跳转中...');
    }
    
    public function delUserByPhone()
    {
    
        $User = M("User");
        $data['phone'] = I('get.phone');
    
        if(!empty($data['phone'])){
            $id = $User->where($data)->delete();
            if($id==false){//删除失败
                $this->ajaxReturn(array('status'=>-2), 'JSON');
            }else{//删除成功
                $this->ajaxReturn(array('status'=>0), 'JSON');
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>-1), 'JSON');
    }
    
    public function addPost()
    {
    
        $Post = M("Post");
        $data['user_id'] = I('post.user_id');
        $data['content'] = I('post.content');
        $data['is_top'] = I('post.is_top');
        $data['song_id'] = I('post.song_id',0);
        $data['create_time'] = date("Y-m-d H:i:s",time());
        $data['modify_time'] = date("Y-m-d H:i:s",time());
        $id = $Post->data($data)->add();
        $status = 0;
        if($id==false){//插入失败
            $status = -1;
        }else{//插入成功
            $upload = new \Think\Upload(); // 实例化上传类
            $upload->maxSize = 0;
            $upload->rootPath = './music/postPng/'; // 设置附件上传根目录
            $upload->exts = array(
                'png'
            ); // 设置附件上传类型
            $upload->replace = true;
            $upload->hash = false;
            $upload->autoSub = false;
            $upload->saveName = $id;
            $info = $upload->upload();
            if($info){
                $Post-> where("id=".$id)->setField('img_url','http://1.117.109.129/core/music/postPng/'.$id.'.png');
            }
        }
        //数据不符合规范
        $this->ajaxReturn(array('status'=>$status), 'JSON');
    }
    
    public function addPostComments()
    {
    
        $PostComments = M("PostComments");
        $Post = M("Post");
        $data['user_id'] = I('get.user_id');
        $data['post_id'] = I('get.post_id');
        $data['content'] = I('get.content');
        $data['parent'] = I('get.parent');
        $data['create_time'] = date("Y-m-d H:i:s",time());
        $data['modify_time'] = date("Y-m-d H:i:s",time());
        $id = $PostComments->data($data)->add();
        if($id==false){//插入失败
            $status = -1;
        }else{//插入成功
            $Post-> where("id=".$data['post_id'])->setField('modify_time',date("Y-m-d H:i:s",time()));
            $status = 0;
        }
        $this->ajaxReturn(array('status'=>$status), 'JSON');
    }
    
    public function getPosts()
    {
        $Post = M("Post");
        $start = I('get.start');
        $length = I('get.length');
        $posts = $Post->order('is_top desc,modify_time desc')->limit($start,$length)->select();
        if($posts===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($posts)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            foreach ($posts as &$post) {
                $User = M("User");
                $post['user_name'] = $User->where("id='%s'",$post['user_id'])->getField('name');
                $post['phone'] = $User->where("id='%s'",$post['user_id'])->getField('phone');
                $post['gender'] = $User->where("id='%s'",$post['user_id'])->getField('gender');
                $PostComments = M("PostComments");
                $post['comments_count'] = $PostComments->where("post_id='%s'",$post['id'])->count();
                $Song = M("Song");
                $song = $Song->where("id='%s'",$post['song_id'])->find();
                $post['song'] = $song;
            }
            $ret['status']=0;
            $ret['items']=$posts;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getPostComments()
    {
        $PostComment = M("PostComments");
        $postId = I('get.post_id');
        $postComments = $PostComment->where("post_id=".$postId)->order('modify_time desc')->select();
        if($postComments===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($postComments)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            foreach ($postComments as &$postComment) {
                $User = M("User");
                $postComment['user_name'] = $User->where("id='%s'",$postComment['user_id'])->getField('name');
                $postComment['phone'] = $User->where("id='%s'",$postComment['user_id'])->getField('phone');
                $postComment['gender'] = $User->where("id='%s'",$postComment['user_id'])->getField('gender');
            }
            $ret['status']=0;
            $ret['items']=$postComments;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getbanners()
    {
        $Banner = M("Banner");
        $banner = $Banner->order('modify_time desc')->select();
        if($banner===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($banner)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['items']=$banner;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function upComment()
    {
        $Comments = M("Comments");
        $id = I('get.id');
        if($Comments-> where("id='%s'",$id)->setInc('up_count')==false){
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//更新成功
            $this->ajaxReturn(array('status'=>0), 'JSON');
        }
    }
    
    public function getRobotUser()
    {
        $num = I('get.num');
        $User = M("User");
        $users = $User->where("robot=1")->order('rand()')->limit($num)->select();
        if($users===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($users)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['items']=$users;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getHaogeci()
    {
        $Song = M("Song");
        $songs = $Song->where("is_haogeci=1")->order('modify_time desc')->select();
        if($songs===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            foreach ($songs as &$song) {
                $lrcUrl = self::ONLINEHEAD.'/music/lrc/'.$song['code'].'.lrc';
                $lrc = file_get_contents($lrcUrl);
                $contents = explode(":",$lrc);
                $song['title'] = $contents[0];
                $song['geci'] = $contents[1];
                $User = M("User");
                $song['user_name'] = $User->where("id='%s'",$song['user_id'])->getField('name');
            }
            $ret['status']=0;
            $ret['items']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function addGedan()
    {
    
        $Gedan = M("Gedan");
        $data['name'] = I('post.name');
        $data['create_time'] = date("Y-m-d H:i:s",time());
        $data['modify_time'] = date("Y-m-d H:i:s",time());
        $data['status'] = 0;
        $id = $Gedan->data($data)->add();
        $status = -1;
        if($id==false){//插入失败
            return;
        }else{//插入成功
            $upload = new \Think\Upload(); // 实例化上传类
            $upload->maxSize = 0;
            $upload->rootPath = './music/gedanPng/'; // 设置附件上传根目录
            $upload->exts = array(
                'png'
            ); // 设置附件上传类型
            $upload->replace = true;
            $upload->hash = false;
            $upload->autoSub = false;
            $upload->saveName = $id;
            $info = $upload->upload();
            if($info){
                $Gedan-> where("id=".$id)->setField('img','http://1.117.109.129/core/music/gedanPng/'.$id.'.png');
                $status = 0;
                redirect('http://1.117.109.129/php/home/admin/gedanList', 1, '页面跳转中...');
            }
        }
    }
    
    public function getGedans()
    {
        $Gedan = M("Gedan");
        $gedans = $Gedan-> where("status=1")->order('modify_time desc')->select();
        if($gedans===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($gedans)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $ret['status']=0;
            $ret['items']=$gedans;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getGedanSong()
    {
        $Song = M("Song");
        $start = I('get.start',0);
        $length = I('get.length',100);
        $gedan_id = I('get.gedan_id');
        $songs = $Song->where("gedan_id='%d'",$gedan_id)->limit($start,$length)->order('modify_time desc')->select();
        if($songs===false){
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($songs)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{
            $ret['status']=0;
            $ret['items']=$songs;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function addTemplate()
    {
        header("Access-Control-Allow-Origin:*");
        $Template = M("Template");
        $data['name'] = I('post.name');
        $data['user_id'] = I('post.user_id');
        $data['singer'] = I('post.singer');
        $data['price'] = I('post.price');
        $User = M("User");
        $data['user_name'] = $User->where("id='%s'",$data['user_id'])->getField('name');
        $data['create_time'] = date("Y-m-d H:i:s",time());
        $data['modify_time'] = date("Y-m-d H:i:s",time());
        $data['status'] = 0;
        $id = $Template->data($data)->add();
        if($id==false){//插入失败
            return;
        }else{//插入成功
            $upload = new \Think\Upload(); // 实例化上传类
            $upload->maxSize = 0;
            $upload->rootPath = './music/zouyin/'; // 设置附件上传根目录
            $upload->exts = array(
                'mid','wav','lrc','time'
            ); // 设置附件上传类型
            $upload->replace = true;
            $upload->hash = false;
            $upload->autoSub = false;
            $upload->saveName = $id;
            $info = $upload->upload();
            if($info){
                $Template-> where("id=".$id)->setField('lrc','http://1.117.109.129/core/music/zouyin/'.$id.'.lrc');
                $Template-> where("id=".$id)->setField('mid','http://1.117.109.129/core/music/zouyin/'.$id.'.mid');
                $Template-> where("id=".$id)->setField('time_file','http://1.117.109.129/core/music/zouyin/'.$id.'.time');
                $Template-> where("id=".$id)->setField('acc_wav','http://1.117.109.129/core/music/zouyin/'.$id.'.wav');
                exec('/usr/bin/lame /data/music/zouyin/'.$id.'.wav', $result, $status);
                $Template-> where("id=".$id)->setField('acc_mp3','http://1.117.109.129/core/music/zouyin/'.$id.'.mp3');
                exec('sh ./changemidi.sh /data/music/zouyin/'.$id.'.mid', $result, $status);
                redirect('http://1.117.109.129/php/home/admin/upload', 1, '页面跳转中...');
            }else{
                echo $upload->getError();
            }
        }
    }
    
    public function getTemplate()
    {
        $Template = M("Template");
        $templates = $Template->where('status=1')->order('modify_time desc')->select();
        if($templates===false){
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($templates)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{
            $ret['status']=0;
            $ret['items']=$templates;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getTemplateById()
    {
        $Template = M("Template");
        $data['id'] = I('get.id');
        $template = $Template->where($data)->find();
        if($template===false){//查询出错
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($template)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            $template['status']=0;
            $this->ajaxReturn($template, 'JSON');
        }
    }
    
    public function delTemplate()
    {
        $Template = M("Template");
        $data['id'] = I('get.id');
        $id = $Template->where($data)->delete();
        if($id==false){//删除失败
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else{//删除成功
            $this->ajaxReturn(array('status'=>0), 'JSON');
        }
    }
    
    
    public function search()
    {
        $name = I('get.name');
        $User = M('User');
        $Song = M('Song');
        $query_song['title'] = array('like','%'.$name.'%');
        $songs = $Song->where($query_song)->limit(100)->select();
        $ret['status']=-1;
        if($songs){
            $ret['status']=0;
            $ret['songs']=$songs;
        }
        $query_user['name'] = array('like','%'.$name.'%');
        $users = $User->where($query_user)->limit(100)->select();
        if($users){
            $ret['status']=0;
            $ret['users']=$users;
        }
        $this->ajaxReturn($ret, 'JSON');
        
    }

    public function getGiftBySongId()
    {
        $Gift = M('Gift');
        $song_id = I('get.song_id');
        $gifts = $Gift->where("song_id='%d'",$song_id)->order('create_time desc')->select();
        if($gifts===false){
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($gifts)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{
            foreach ($gifts as &$gift) {
                $User = M("User");
                $gift['user_name'] = $User->where("id='%s'",$gift['user_id'])->getField('name');
                $gift['phone'] = $User->where("id='%s'",$gift['user_id'])->getField('phone');
            }
            $ret['status']=0;
            $ret['items']=$gifts;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    
    public function getTemplateBuyByUserId()
    {
        $TemplateBuy = M('TemplateBuy');
        $user_id = I('get.user_id');
        $templateBuys = $TemplateBuy->where("user_id='%d'",$user_id)->order('create_time desc')->select();
        if($templateBuys===false){
            $this->ajaxReturn(array('status'=>-2), 'JSON');
        }else if(empty($templateBuys)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{
            $ret['status']=0;
            $ret['items']=$templateBuys;
            $this->ajaxReturn($ret, 'JSON');
        }
    }
    

    public function addTemplateBuy()
    {
    
        $TemplateBuy = M('TemplateBuy');
        $data['user_id'] = I('get.user_id');
        $data['template_id'] = I('get.template_id');
        $data['create_time'] = date("Y-m-d H:i:s",time());
        $id = $TemplateBuy->data($data)->add();
        if($id==false){//插入失败
            $status = -1;
        }else{//插入成功
            $status = 0;
        }
        $this->ajaxReturn(array('status'=>$status), 'JSON');
    }
    
    
    
    
    
    

}

?>