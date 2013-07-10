base on  [slim framework](http://slimframework.com/ "slim")

Because of this and become wonderful

###Main Features
* **Easy**  

* **Lightweight**  

* **Powerful**   Add mvc features

###Route#
 You do not have to write each controller route as before,Only in the following cases:

 $app->get('/con', 'saeed.index');

 main use for site rewrite . format :  dir.controller.action<br/>

###Controller#
    <code>
       class indexController extends Controller{

           public function index(){
               $this->render('index',array('data'=>'test'));
           }
           public function none(){
               $this->response->status('404');

           }
       }
    </code>


###Model###View
developing....
