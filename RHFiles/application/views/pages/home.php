  <body>
    
    <!-- Main container  -->
    <section id="main-stage" style="padding-top:0%;margin-top:0%">

        <!-- First background  -->
        <div class="bgimg-1">
        
          <!-- Large logo  -->
          <div class="caption-logo">
            <img src="<?php echo base_url(); ?>images/splash-page/logo-big.png">
          </div>

          <!-- Boucing arrow -->
          <div class="arrow bounce">
            <img src="<?php echo base_url(); ?>images/splash-page/double-arrow.png" width="100" height="100">
          </div>

        </div>


        <div class="home-tiles">
          <div class="container">

            <!-- Information columns  -->
            <div class="row">
              <div class="four columns home-tile" >
                <img src="<?php echo base_url(); ?>images/splash-page/satelite-icon.png" height="225" width="225"> 
                <h3 style="text-align:center;">CONNECT</h3>
                <p>Meet students you never knew existed on the royal holloway campus through your passion of a hobby or skill.</p>
              </div>
              <div class="four columns home-tile" >
              <img src="<?php echo base_url(); ?>images/splash-page/alien-icon.png" height="225" width="225"> 
                <h3 style="text-align:center;">EXCHANGE</h3>
                <p>Not only improve your teaching abilities but also learn a new skill during the exchange with another student.</p>
              </div>
              <div class="four columns home-tile">
                <img src="<?php echo base_url(); ?>images/splash-page/atom-icon.png" height="225" width="225"> 
                <h3 style="text-align:center;">LEARN</h3>
                <p>Learn a variety of new skills from like minded individuals at all multitude of different levels using the trading system. </p>
              </div>
            </div>

          </div>
        </div>

      <!-- Second background  -->
      <div class="bgimg-2">
        <div class="caption-logo margin-15">
          <span class="border"><a href="<?php echo site_url(); ?>users/login">LOG IN &#9002;</a></span>
        </div>  
      </div>


      <div style="position:relative;">
        <div class="banner">
          <h1 class="centered">TOP TRADERS</h1>
        </div>
      </div>

      <!-- Third background  -->
      <div class="bgimg-3">
        <div class="border" id="leaderboard-container">

          <!-- Top 3 Leaderboard  -->
          <div class="leaderboard" id="no1">
              <p>#1</p>
              <?php if(sizeof($top_profiles)>=1): ?>
                <a href="<?php echo base_url()."profiles/view/".$top_profiles[0]['user_id']; ?>"><img src="<?php echo $pics[0];?>"></a>
                <span><?php echo $top_profiles[0]['username'];?></span> 
              <?php endif; ?>
          </div>

          <div class="leaderboard" id="no2">
              <p>#2</p>
              <?php if(sizeof($top_profiles)>=2): ?>
                <a href="<?php echo base_url()."profiles/view/".$top_profiles[1]['user_id']; ?>"><img src="<?php echo $pics[1];?>"></a>
                <span><?php echo $top_profiles[1]['username'];?></span>
              <?php endif; ?>
          </div>

          <div class="leaderboard" id="no3">
              <p>#3</p>
              <?php if(sizeof($top_profiles)>=3): ?>
                <a href="<?php echo base_url()."profiles/view/".$top_profiles[2]['user_id']; ?>"><img src="<?php echo $pics[2]; ?>"></a>
                <span><?php echo $top_profiles[2]['username'];?></span>
              <?php endif; ?>
          </div>

        </div>   
      </div>

      <div style="position:relative;">
        <div class="banner">
          <h1 class="centered">NO ACCOUNT YET?</h1>
        </div>
      </div>

      <!-- Fourth background  -->
      <div class="bgimg-4">
        <div class="caption-logo margin-15">
          <span class="border"><a href="<?php echo site_url(); ?>users/register">SIGN UP &#9002;</a></span>
        </div>
      </div>

    </section><!-- Close main Container-->
    
  </body>

</html>
  
