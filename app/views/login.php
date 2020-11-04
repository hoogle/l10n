        <div class="panel-heading"> 
                <h3 class="text-center"> Sign In to <strong class="text-custom">Astra l10n</strong> </h3>
            </div> 


            <div class="panel-body">
            <form name="signIn" class="form-horizontal m-t-20" method="POST" action="/index/login" data-parsley-validate novalidate>
                
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" name="email" type="email" required="" placeholder="Username" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" name="passwd" type="password" required="" placeholder="Password" required>
                    </div>
                </div>
                
                <div class="form-group text-center m-t-40">
                    <div class="col-xs-12">
                        <button class="btn btn-pink btn-block text-uppercase waves-effect waves-light" type="submit" disabled>Log In</button>
                    </div>
                </div>
            </form> 
            
            </div>   
            </div>                              
