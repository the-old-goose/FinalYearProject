
<!-- single skill view -->
<?php $this->load->library('availability'); ?>

<div class="container " style="padding-top:2%;">
  
  <!-- Next Skill Button -->
  <?php if(isset($_SESSION['user_id'])):?>
    <?php if(!$my_skill):?>
      <?php if(!is_null($proposers_skill['skill_id'])): ?>
        <div class="row" style="margin-bottom:5px;">
          <div id="skill-selector" class="four columns" > 
          <a href="<?php echo site_url().'skills/view/'.$skill['skill_id'].'/'.($user_skill_offset+1);?>"><h5>Next Skill &#8594;</h5></a>
          </div>
        </div> 
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>


  <div class="row">

    <!-- initial profile pic  -->
    <div id ="profile-pic-column" class="four columns">
      <a href="<?php echo site_url().'profiles/view/'.$skill['user_id']; ?>"><div id="skill-picture"><img src="<?php echo $skill['picture'];?>"></div></a>
    </div>

    <div id ="user-action-column" class="four columns">

    <?php if($this->session->flashdata('proposal_error')): ?>
        <p><?php echo $this->session->flashdata('proposal_error'); ?></p>
    <?php endif; ?>

    <!-- Offer button  -->
    <?php if($this->session->userdata('user_id')):?>
      <?php if(!$no_results): ?>
        <?php if(!is_null($proposers_skill['skill_id']) && !is_null($skill['skill_id'] )): ?>
          <?php echo form_open('trades/initiate/'.$skill['skill_id'].'/'.$proposers_skill['skill_id'],'style="padding-top: 35px;"');?>
          <input id="btn" type='submit' value='Offer'>
        <?php endif;?>
      <?php endif; ?>
      <?php endif; ?>

      <!-- Calendar -->
      <div class="skill-calendar">
        <div id="month" class="row">
          <div class="twelve columns">
              <ul>
                <li><?php echo date('F');?><br><span style="font-size:34px"><?php echo date('Y'); ?></span></li>
              </ul>
          </div>
        </div>

        <!-- Calendar body  -->
        <div id="skill-calendar-body" class="row">
          <table style="width:100%; height:100%;">
            <tr>
              <?php $this->load->library('availability'); ?>
              <?php $rowcount=1; ?>
              <?php for ($x = 1;$x<=$calendar['max-days']-1;$x++) : ?>
                  <td id="skill-td"><span><input type="checkbox" class="skill-checkbox" name="sel-date[]" id="<?php echo date('M').$calendar['today'];?>" value="<?php echo $this->availability->build_date($calendar['today']);?>"><label style="display:inline; font-weight:599" for="<?php echo date('M').$calendar['today'];?>"><?php echo $this->availability->ordinal($calendar['today']); ?></label></span></td>
                  
                <?php if($calendar['month-days']==$calendar['today']): ?>
                            <tr id="next-month"><td style="padding-left: 40%;" colspan="7"><?php echo date('F',strtotime('+1 month')); ?></td></tr>
                            <?php $calendar['today']=1;?>
                            <td id="skill-td"><span><input style="margin-bottom: 1px;" type="checkbox" name="sel-date[]" value="<?php echo $this->availability->build_date($calendar['today']);?>"><label style="display:inline; font-weight:599;"  for="<?php echo date('M').$calendar['today'];?>"><?php echo $this->availability->ordinal($calendar['today']); ?></label></span></td>
                            <?php $rowcount=($calendar['today']%$rowcount);?>
                <?php endif; ?>

                  <?php if($rowcount%7==0  ): ?>
                      </tr><tr> 
                  <?php endif; ?>  

                  <?php $rowcount++;?>
                  <?php $calendar['today']++;?> 

              <?php endfor;?>  

            </tr>
          </table>  
        </div>
      </div>

    </div>

    <!-- users profile  -->
    <div id ="profile-pic-column" class="four columns">
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="<?php echo site_url().'profiles/view/'.$proposers_skill['user_id']; ?>"><div id="skill-picture"><img src="<?php echo $proposers_skill['picture'];?>"></div></a>
      <?php else : ?>
        <h1 style="font-size:20rem; padding-left:13%;">&#9785;</h1>     
      <?php endif; ?>
    </div>      
  </div>
    

  <div class="row">

    <div id ="skill-detail-column" class="four columns">
      
      <!-- Initial skill  -->
      <?php if(is_null($skill['skill_id'])): ?>
        <p id="error">ERROR:<br> This skill does not exist </p>
      <?php else:?>
        <div id="title-div"><h3 id="skill-name"><?php echo $skill['name'];?></h3><h3 id="skill-value"><?php echo $skill['value'];?></h3></div>
        <p><?php echo $skill['description'];?></p>
      <?php endif;?>

    </div>

    

    <div id ="skill-detail-column" class="four columns" style="float:right;">
      
      <!-- users skill -->
      <?php if(isset($_SESSION['user_id'])):?>
          <?php if(!$my_skill):?>
            <?php if(is_null($proposers_skill['skill_id'])): ?>
              <h3>You Have No listings yet.</h3>
            <?php else: ?>
              <div id="title-div"><h3 id="skill-name"><?php echo $proposers_skill['name'];?></h3><h3 id="skill-value"><?php echo $proposers_skill['value'];?></h3></div>
              <p><?php echo $proposers_skill['description'];?></p>
              <?php echo form_close(); ?>
            <?php endif; ?>
          <?php else : ?> 
            <h3>This is your skill!</h3>
          <?php endif; ?>
      <?php else: ?>
        <h3>Please login to see your skills.</h3>
      <?php endif; ?>
    </div>

    
  </div>

</div>