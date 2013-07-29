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

       class indexController extends Controller{

           public function index(){
               $this->render('index',array('data'=>'test'));
           }
           public function none(){
               $this->response->status('404');

           }
           
           
       }


###Model

base on [Medoo](http://medoo.in/) Library ,  please see [document](http://medoo.in/doc)

but have many differences

        class testModel extends Model{

            public function __construct(){
                parent::__construct();
            }
            //the tablename just write here , medoo functions' parameters about table  removed:
            protected $dbconfig = 'test';
            protected $table = 'test'; 

        }

how to use

        class SaeedController{
    
            public function index(){
                load_model('test');
                $testmodel = new testModel();
                $result = $testmodel->select("*");
                print_r($result);
            }
            public function fetchall(){
                //you can also use this method to get an database instance
                $database = new Medoo();
                $database = $database->loadconfig('test')->table('test');
                $result = $database->select("*");
                print_r($result);
            }


        }

###View

###Rewrite

   try_files $uri $uri/ /index.php;

###Other

load_view(); 

how to use :

you can use in template or other position,$status true or false ,default false,
if true it will return $tempaltefile as a varible instead of output directly;
load_view($templatefile,$data,$status);

if you have any question,contact blandlove123@gmail.com
