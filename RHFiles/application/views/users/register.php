<div class="row top-padding">

  <div class="four columns full-height"> 
    <?php echo validation_errors(); ?>
  </div>

  <div class="four columns" style="height:96%;">

    <!-- register form -->
    <div id="signup">
      <?php echo form_open('users/register');?>
          <h1>Register</h1>
          <label>First Name</label>
          <input type='text' maxlength='15' name='first_name' placeholder='Your First name' value="<?php echo $this->session->flash_firstname ;?>"><br>
          <label>Last Name</label>
          <input type='text' maxlength='15' name='last_name' placeholder='Your Last name' value="<?php echo $this->session->flash_lastname ;?>"><br>
          <label>Username</label>
          <input type='text' maxlength='15' name='user' placeholder='At least 5 Characters long' value="<?php echo $this->session->flash_username ;?>"><br>
          <label>Email</label>
          <input type='text' maxlength='255' name='email' placeholder='Valid Email address' value="<?php echo $this->session->flash_email ;?>"><br>
          <label>Password</label>
          <input type='password' maxlength='72' name='pass' placeholder='At least 6 Characters long' ><br>
          <label>Mobile</label>
          <input type='text' maxlength='11' name='mobile' placeholder='11 digits long' value="<?php echo $this->session->flash_mobile ;?>"><br>
          <span class='fieldname'>&nbsp;</span>
          <label>Halls</label>
          <select  name="locationid" style="width:70%;">
              <option value="1">Founders</option>
              <option value="2">Reid</option>
              <option value="3">EngleField Green</option>
              <option value="4">Williamson</option>
              <option value="5">Tuke</option>
              <option value="6">Butler</option>
              <option value="7">George Eliot</option>
              <option value="8">Another Galaxy</option>
          </select>
          <br>
          <input class='btn' type='submit' value='Register'>
      <?php echo form_close(); ?>
    </div>
    
  </div>

  <div class="four columns full-height"></div>

</div>