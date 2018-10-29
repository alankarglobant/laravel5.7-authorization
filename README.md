# laravel5.7-authorization
Authorization in Laravel 5.7 framework using Gates and Policies.

# Installation 
Clone this repository via `git clone` command or just download a zip file and extract in your web root. 
After cloning `cd` to your cloned directory and make following folders.

- PROJECT_ROOT_DIR/`storage`
- PROJECT_ROOT_DIR/storage/`app`
- PROJECT_ROOT_DIR/storage/`framework`
  - PROJECT_ROOT_DIR/storage/framework/`cache`
  - PROJECT_ROOT_DIR/storage/framework/`sessions`
  - PROJECT_ROOT_DIR/storage/framework/`testing`
  - PROJECT_ROOT_DIR/storage/framework/`views`
- PROJECT_ROOT_DIR/storage/`logs`
  - PROJECT_ROOT_DIR/storage/logs/`laravel.log`

After creating all above folders please assign read / write or full access permission to these folders via `chmod` command.

`chmod - R 777 PROJECT_ROOT_DIR/storage`

Now get the Laravel vendor files via composer. For that go to your PROJECT_ROOT_DIR and execute command:

-  `composer update` - This will donwload all the necessary framework files under the vendor folders. 

Copy `.env.example` into `.env` and set necessary configuration like database. Alos if you want you can set configuration values for `mail` , `pusher` etc. 

After setting the database credentials from let's create the necessary tables. For that from your PROJECT_ROOT directory execute below commands. 

- `php artisan migrate:install` : This will create the migration table in the database
- `php artisan migrate` : This will run all the migrations and creates the tables.
- `php artisan db:seed` : This will insert the predefined data into the tables. 

Once all above command executes run your application on browser and register user. 
For running application if you are using 
- xampp / wamp : http://localhost/PROJECT_DIR_NAME/public/index.php
- if not then go to your project root directory and execute: `php artisan serve` This will start php internal server and you can access
your application on http://localhost:8000

Consider shopping cart application where a user with a vendor role creates / update / delete the product details. Now, as the vendor is quite busy with his product marketing and other stuff he has appointed a person for maintaining the products of specific categories. 

So here is the flow of the above use case
- User registered with an application as a vendor 
- The vendor can create/ modify/remove products  (As he is the owner he has all access to all actions ) 
- The vendor can create staff member under his account.
- Now, the staff member who has access can perform the tasks like modify and view.
- The end user can only able to see the details of the product.  

Let’s understand the code for the same. 
By skipping the basics of CRUD operation for product and user creation let’s jump on the authorization part. Before that here are some basic details about the directory structure.

- app - Holds all the application files
- app\Http - application controllers, services and custom request classes.
- app\Policies - all policy files 
- app\Providers - all the application providers.


For our example, we have the following controllers.
   	
- app\Http\Controllers\Auth\:
    - ForgetPasswordController - For sending forget password email with the reset link
    - LoginController - For accessing the system via the login form
    - RegisterController - As the name implies it is used for registering a user with the system
    - ResetPasswordController - Allowing a user to reset the password via reset password link.
    - VerificationController - responsible for handling email verification for any user that recently registered with the application.

- app\Requests\:
    - CreateProductRequest: Performs a set of validation before storing or updating the product details. 
    - CreateStaffRequest: Performs a set of validation before creating or updating the staff details. 
    - DeleteProductRequest: Performs a set of validations before deleting the product from the system.

- app\Providers\:
    - AppServiceProvider: Register any application services.
    - AuthServiceProvider: Holds the policy mappings for the application & Register any authentication/authorization services.
Other few more providers are available like BroadcastServiceProvider, EventServiceProvider, RouteServiceProvider. More information about these providers is available on [Laravel Providers](https://laravel.com/docs/5.7/providers)

- app\Policies - Holds all the resource policy classes under this directory. 
- Models are residing in app directory only.

For authorizing any resource we are using Gate and Policy method. For creating a policy for our resource/model like Product we can use artisan command like 

`php artisan make:policy -m Product`

Here with -m we are specifying the model name we can create the policy for product model. It will create new class ProductPolicy under the app\Policies directory.  ( If you used the --model option when generating your policy via the Artisan console, it will already contain methods for the view, create, update, and delete actions.)

The `make:policy` command will generate an empty policy class. If we have passed the model name by using -m or --model option then the policy class is already included with the basic `CRUD` policy methods.

As our application should know about the policies we are generated for that we have to register the policy. Policies are being registered under the `AuthServiceProvider` class. This `AuthServiceProvider` class contains the array named as `policies` which map our Eloquent models to their corresponding policies. In our example, we have to map the `User` & `Product` Model.

  ~~~~
  /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\User' => 'App\Policies\StaffMemberPolicy',
        'App\Product' => 'App\Policies\ProductPolicy',
    ];
~~~~ 
##### Writing policy: 
Once the policy registered we have to specify the methods for each action that has to be authorized. 
In our above example, only the owner ( i.e. the User with vendor role ) can able to create the product. 
Hence we can add create method under the ProductPolicy which will determine if the logged in user has the vendor role and allows to create the product by showing product create form.

The create method will receive a User instance as its arguments and return true or false indicating whether the user is authorized to create the product. So for this scenario, we have to check the users' role id is matches with the vendor role id.


     /**
     * Determine whether the user can create products.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->role->id === Role::ROLE_VENDOR;
    }

##### Methods without models : 
Some policy methods just receive the currently authenticated user and not an  instace of the model is being authorized.
When defining policy methods that will not receive a model instance, such as our above create method, it will not receive a model instance. Instead, you should define the method as only expecting the authenticated user:

Now let’s authorizing actions using policies. 
There are three ways to do it.
- Via User Model.
- Via Middleware.
- Via Controller Helpers. 

######  We have used last option in our example. Here is the way we can use it.

 ~~~~ 
 /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('product.create',['categories' => $this->getCategories()]);
    }
~~~~

As like for creating a  product we have to authorize for updating product details.As per our example use case, the product details can be updated by the vendor himself or the staff member he has created. Let see the code for the same.

~~~~
    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Request\CreateProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateProductRequest $request, $id)
    {
        $product = Product::find($id);
        $this->authorize('update',$product);
        $this->saveProduct($product, $request->all());

        return redirect(route('product.index'))->with('status', 'Product has been modified !');
    }
~~~~

We can also authorize the user for a particular resource from view as well. This we can achieve by using the blade helpers as below. For our example, we are showing the links of edit and delete if that user is authorized to do so while listing the product on the dashboard. Here are the code snippets for the same.

~~~~
@if($products)
                    <ul>
                    @foreach($products as $product)
                        <li>
                            <span> {{$product->name}} | &#8377;{{$product->price}}</span>
                            <div>
                                <span>
                                @can('update', $product) <a href="{{ route('product.edit',['id' => $product->id])}}">Edit</a> | @endcan
                                @can('view', $product) <a href="{{ route('product.show',['id' => $product->id])}}">View</a> | @endcan
                                @can('delete', $product) <a href="javascript:void(0);"
                                onclick="event.preventDefault();
                                                     document.getElementById('delete-form-{{$product->id}}').submit();">Remove</a></span>

                                <form id="delete-form-{{$product->id}}" action="{{ route('product.destroy',['id' => $product->id]) }}" method="POST" style="display: none;">
                                    @method('DELETE')
                                    @csrf
                                </form>
                                @endcan
                            </div>
                            <div>{!! $product->description !!}</div>
                        </li>
                        <br/>
                    @endforeach
                    </ul>
                    @else
                        <div class="alert alert-success" role="alert">
                            No products found <a href="{{route('product.create')}}">Click Here </a> to create;
                        </div>
                    @endif
~~~~
Here in the above code, you can see we have used a `@can` helper function. The first argument is the name of the method from the policy class and the second is the product model instance.

###### Gates: (This is not covered in the above example )
Gates are Closures that determine if a user is authorized to perform a given action and are typically defined in the `App\Providers\AuthServiceProvider` class using the `Gate` facade.

: Gates always receive a user instance as their first argument and may optionally receive additional arguments such as a relevant Eloquent model. 

##### Defining Gates:
Let’s for an example user can update their post only this we can achieve via gates as: (This code has to be placed under the `App\Providers\AuthServiceProvider` class ).

~~~~
/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{authentication/authorization
    $this->registerPolicies();

    Gate::define('update-post', function ($user, $post) {
        return $user->id == $post->user_id;
    });
}
~~~~

Authorizing action by using gates allows and denies methods. 
E.g. ( The below code will reside in a particular controller which action you want to authorize. )

~~~~
if (Gate::allows('update-post', $post)) {
   // The current user can update the post...
}

if (Gate::denies('update-post', $post)) {
   // The current user can't update the post...
}
~~~~

If we like to determine if a particular user is authorized to perform an action, we may use the forUser method on Gate facade. ( The below code will reside in a particular controller which action you want to authorize. )

~~~~
if (Gate::forUser($user)->allows('update-post', $post)) {
   // The user can update the post...
}

if (Gate::forUser($user)->denies('update-post', $post)) {
   // The user can't update the post...
}
~~~~
More details about the Authorization in Laravel is available here: 

[Laravel Authorization Documentation ](https://laravel.com/docs/5.7/authorization)


