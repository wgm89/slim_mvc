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


###Model

base on [Medoo](http://medoo.in/) Library ,  please see [document](http://medoo.in/doc)

but have many diffrences

<code>
	class testModel extends Model{

        public function __construct(){
            parent::__construct();
        }
        protected $table = 'test'; //the tablename just write here , medoo functions' parameters about table  removed:

    }

</code>
###View
