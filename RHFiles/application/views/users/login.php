<?php echo validation_errors();?>

<div class="row top-padding">

    <div class="four columns full-height"></div>

      <div class="four columns height-40">
        <!--Login Form -->
        <div id="signup">
                                
          <?php echo form_open('users/login');?>

                <h1>Log In</h1>
                <label for='email'>Email</label>
                <input type='text' maxlength='255' name='email' value='hoosive@hotmail.co.uk'><br>
                <label for='pass'>Password</label>
                <input type='password' maxlength='40' name='pass' value='password1'><br>
                <input class='btn' type='submit' value='Login'>

          <?php echo form_close();?>
                
        </div>
      </div>

    <div class="four columns full-height"></div>

</div>

