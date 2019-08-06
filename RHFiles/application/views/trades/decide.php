<div class="container tile extend-stage standard-padding"> 

    <!--Title -->
    <div class="row title-row">
        <h1>Pending Offer</h1>   
    </div>

    <?php if($noresults): ?>
        <p>Sorry this trade does not exist</p>
    <?php else: ?>

        <!--Progress bar -->
        <div class="progress">
            <h4>Progress 25% <progress value="25" max="100"></progress></h2>
        </div>

        <!--users and skills -->
        <div class="row" >
            <div class="four columns decide-info">
                <h3><?php echo $proposal['name']; ?></h3>
                <div id="you-pic" ><p style="padding-top: 40%;">YOU</p></div> 
                <p class="grey-heading">You</p>
            </div>
            <div class="three columns decide-info" style="box-shadow: none; padding-top:8%;">
                <img id="handshake-icon" src="<?php echo base_url(); ?>images/icons/handshake-icon.png" > 
            </div>
            <div class="four columns decide-info">
                <h3><a style="font-size: 3.6rem;" href="<?php echo site_url('/skills/view/'.$proposal['offer_skill_id']); ?>" ><?php echo $proposal['offered_skill'] ;?></a></h3>
                <img id="decide-pic" src="<?php echo $proposal['pic']; ?>"> 
                <p class="grey-heading"><a class="grey-heading" href="<?php echo site_url('/profiles/view/'.$proposal['user_id']); ?>" ><?php echo $proposal['username'] ;?></a></p>
            </div>
        </div>

        <!--Instructions -->
        <div class="row" id="calendar-instructions">
            <div class="eleven columns">
                <p> 
                It is your choice to either decline the offer or select a time you are available that matches 
                the users availibility in the next two weeks using the timetable below.
                </p>
            </div>
        </div>

        <!--Calendar -->
        <div id="calendar" class="row">
           <div id="month" class="row">
              <div class="twelve columns">
                <ul>
                   <li><?php echo date('F');?><br></li>
                   <li><span style="font-size:34px">2019</span></li>
                </ul>
              </div>
              <?php echo form_open('trades/accept_proposal/'. $proposal['skill_proposal_id']); ?>
              <div class="row">
                  <input type="submit" value="Accept">
                  <input type="button" value="Decline" onclick="window.location.href='<?php echo site_url().'trades/cancel_proposal/'.$proposal['skill_proposal_id']; ?>'">
              </div>
            </div>
            <div id="calendar-body" class="row">
                <table style="width:100%; height:100%;">
                  <tr>
                  <?php for ($x = 1;$x<=$calendar['max-days'];$x++) : ?>
                      <?php if(array_key_exists(date('M').$calendar['today'],$calendar['proposed_dates']) ||array_key_exists(date('M',strtotime('+1 month')).$calendar['today'],$calendar['proposed_dates']) ) :?>
                         <td id="avail"><?php echo $calendar['today']; ?><input type="radio" name="avail_selected" value='<?php echo $calendar['today']; ?>'></td>
                      <?php else : ?>
                         <td><?php echo $calendar['today'];?></td>
                      <?php endif; ?>
                      
                      <?php if($calendar['month-days']==$calendar['today']): ?>
                            <?php $calendar['today']=1;?>
                            <td><?php echo $calendar['today'];?></td>
                            <?php $x++;?>
                      <?php endif; ?>
                        
                      <?php if($x%7==0  ): ?>
                           </tr><tr> 
                      <?php endif; ?>
                      
                      <?php $calendar['today']++;?>

                  <?php endfor; ?>
                  </tr>
                
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>