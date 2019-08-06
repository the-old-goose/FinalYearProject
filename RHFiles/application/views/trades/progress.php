<div class="container tile standard-padding"> 

    <!--Title -->
    <div class="row title-row">
      <h1>PROGRESS OF TRADE</h1>
    </div>

    
    <div class="row" id="status-row">
        <?php if($status):?>
            <!--In progress trade -->
            <?php if($status=='inprogress'): ?>
                <div class="progress">
                    <h4>Progress 75% <progress value="75" max="100"></progress></h2>
                </div>

                <!--Instructions -->
                <div class="row" id="calendar-instructions">
                    <div class="eleven columns">
                        <p>Great you are on your way to completing a trade!<br>
                        <div id="details" style="text-align:center;">
                            <a style="font-size:30px;"><?php echo 'User:  '. $proposal['username'];?></a><br>
                            <a style="font-size:30px;"><?php echo 'Date:  '.$date ?></a><br>
                            <a style="font-size:30px;"><?php echo 'Mobile:  '. $proposal['mobile'];?></a><br><br>
                        </div>
                            Please contact this user to meet at a time you arrange on the specified date.<br>
                            Press the Complete trade button once you have successfully completed the trade, or press cancel if the trade was unable to happen.
                        </p>
                    </div>
                </div>

                <!--Complete/cancel trade buttons -->
                <div class="row centered">
                  <div class="eleven columns">
                    <input type="button" value="Mark Complete" onclick="window.location.href='<?php echo site_url(); ?>/trades/complete_trade/<?php echo $skill_proposal_id; ?>'">
                    <input type="button" value="Cancel" onclick="window.location.href='<?php echo site_url().'trades/cancel_proposal/'.$skill_proposal_id; ?>'">
                  </div>
                </div>


        <?php elseif($status=='pending approval'): ?>
            <!--Pending approval -->
            <h4>Progress 25% <progress value="25" max="100"></progress></h2>
            <h3>The owner of this proposal still needs to accept or decline the offer</h2>

        <?php elseif($status=='complete'): ?>
            <!--Complete -->
            <h4>Progress 100% <progress value="100" max="100"></progress></h2>
            <h3>Trade successfully completed!</h2>
            <div style="text-align:center; color:#ED6A24;">
                <h1>&#9734; &#9734; &#9734; &#9734; &#9734;</h1>
                <h4>Reviews coming soon!</h3>
            </div>

        <?php elseif($status=='rejected'): ?>
            <!--Rejected -->
            <h4>Progress 0% <progress value="0" max="100"></progress></h2>
            <h3>Unfortunatly this trade was unable to take place, maybe next time.</h2>
        <?php endif; ?>

        <?php else:?>
            <h3> This Trade does not exist or you do not have permission to view this page.</h3>
        <?php endif;?>

    </div>

</div>