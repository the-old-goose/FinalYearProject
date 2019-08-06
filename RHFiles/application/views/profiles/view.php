<script> // Display element on click
              function myFunction() 
              {
              var grav_dialog = document.getElementById("gravatar-dialog");
              if (grav_dialog.style.display === "none") 
                 {
                    grav_dialog.style.display = "block";
                 } 
              else 
                  {
                     grav_dialog.style.display = "none";
                  }
                } 
</script>

<div class="container tile standard-padding"> 
  
    <?php if (!$no_results): ?> 
      <!-- Title and Update button -->
      <div class="row title-row">
        <div class="twelve columns">
          <h1>Profile<?php echo validation_errors(); ?> </h1>
            <?php if ($myprofile):?>
              <input type="button" value="Edit" onclick="window.location.href='<?php echo site_url().'profiles/view/'.$_SESSION['user_id'].'/1';?>'" />
              <?php if ($edit===1):?>
                <?php echo form_open('profiles/update');?>
                <input class="btn" type="submit" value="Update">
              <?php endif;?> 
            <?php endif;?>   
        </div> 
      </div>

      <div class="row">

        <!-- Picture column -->
        <div class="two columns profile-columns">

          <!-- Profile picture -->
          <div id="lister-picture">
            <img src="<?php echo $gravatar_url; ?>">
          </div>
          
          <!-- Link to gravatar-->
          <?php if ($myprofile):?>
                <span id="upload-link"><a onclick="myFunction()">Upload</a></span>
                <div id="gravatar-dialog" style="display:none;">This Site uses Gravatar! <br>
                  <u>Tip: Use the email you signed up with</u><br>
                  <a id="upload-link" href="https://en.gravatar.com/">Click here to set your Image</a>
                </div>
          <?php endif;?>

        </div>
      
        
        <!-- Profile Details column-->
        <div class="two columns profile-columns">
                  <table style="width:100%;">

                      <tr>
                        <th>First Name:</th>
                        <td><?php echo $profile['first_name'];?>
                            <?php if ($edit===1):?>
                              <input type='text' maxlength='15' name='first_name' value='<?php echo $profile['first_name'];?>'>
                            <?php endif;?> 
                        </td>
                      </tr>

                      <tr>
                        <th>Last Name:</th>
                        <td><?php echo $profile['last_name'];?>
                          <?php if ($edit===1):?>
                            <input type='text' maxlength='15' name='last_name' value='<?php echo $profile['last_name'];?>'>
                          <?php endif;?>
                        </td>
                      </tr>

                      <tr>
                        <th>Username:</th>
                        <td><?php echo $profile['username'];?></td>
                      </tr>
                      <tr>
                        <th>Rank:</th>
                        <td><?php echo $profile['rank']; ?></td>
                      </tr>

                      <tr>
                        <th>Galaxy:</th>
                        <td>
                          <?php echo $profile['location_name']; ?>
                          <?php if ($edit===1):?>
                            <select  name="locationid">
                              <option value="1">Founders</option>
                              <option value="2">Reid</option>
                              <option value="3">EngleField Green</option>
                              <option value="4">Williamson</option>
                              <option value="5">Tuke</option>
                              <option value="6">Butler</option>
                              <option value="7">George Eliot</option>
                              <option value="8">Another Galaxy</option>
                            </select>
                          <?php endif;?> 
                        </td>
                      </tr>
                      
                      <!-- Year dropdown-->
                      <tr>
                        <th>Year:</th>
                        <td>
                          <?php echo $profile['year']; ?>
                          <?php if ($edit===1):?>
                            <select  name="year">
                              <option value="First">First</option>
                              <option value="Second">Second</option>
                              <option value="Third">Third</option>
                              <option value="Fourth">Fourth</option>
                              <option value="Masters">Masters</option>
                              <option value="PhD">PhD</option>
                            </select>
                          <?php endif;?> 
                        </td>
                      </tr>

                      <tr>
                        <th>Score:</th>
                        <td><?php echo $profile['score']; ?></td>
                      </tr>

                      <tr>
                        <th>Description:</th>
                        <td>
                          <?php echo $profile['description']; ?>
                          <?php if ($edit===1):?>
                            <textarea class="u-full-width" maxlength="253" name="profdesc"><?=$profile['description'];?></textarea>
                          <?php endif;?> 
                        </td>
                      </tr>

                      <!-- Profile skill listings-->
                      <tr>
                        <th>Listings:</th>
                        <td>
                          <?php if (!$no_listings):?>
                            <div style="height:100px;width:100%;overflow:auto;">
                              <?php foreach($listings as $listing):?>
                                <a href="<?php echo site_url(); ?>skills/view/<?php echo $listing['skill_id']; ?>"><code><?php echo $listing['name'];?></code></a>
                              <?php endforeach; ?>
                            </div>
                          <?php endif; ?>
                        </td>
                      </tr>


                  </table><!-- Close-->

                  <?php echo form_close(); ?>
        </div><!--Close profile detail column-->

    <?php else: ?>
          
      <div class="row">
        <div class="twelve columns">
          <p>This Profile does not exist</p>
        </div>
      </div>

    <?php endif;?>
      
  </div>
</div><!-- Close main container -->