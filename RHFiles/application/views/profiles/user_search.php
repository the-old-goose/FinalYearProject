<!-- Title -->
<div class="container tile standard-padding"> 
    <div class="row title-row">
        <h1>User Search</h1>   
    </div>
</div>

<!-- Search box-->
<div class="row center height-10" style="padding-top:10px;">
	<div class="twelve columns tile" style="height:65px;">
		<div id="search">

			<?php echo form_open('profiles/search');?>	
			<input type="text" placeholder="Search..." name="search" size="30">
			<input id="btn" type='submit' value='Search'>
			<?php echo form_close();?>

		</div>
	</div>
</div>

<!-- search output -->
<div class="container">
    <?php if(!$no_results): ?>
        <?php foreach($profiles as $profile):?>

        <div class="row profile-result tile center">

            <a href="<?php echo site_url().'profiles/view/'.$profile['user_id']; ?>">
              <div class="three columns">
                <img class="profile-pic-medium" src="<?php echo $profile['image']; ?>">
              </div>
            </a>

            <div class="three columns center-text">
                <li><?php echo $profile['first_name']; ?></li>
            </div>

            <div class="three columns center-text">
                <li><?php echo $profile['last_name']; ?></li>
            </div>

            <div class="three columns center-text">
                <li><?php echo $profile['halls']; ?></li>
            </div>
                    
                 
        </div>
        
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Pagination -->
<div class="row">
	<div class="pagination tile center">
	  <?php echo $this->pagination->create_links();?>
	</div>
</div>