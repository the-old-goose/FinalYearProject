<!--search bar-->
<div class="row center height-10">
	<div class="twelve columns tile" style="height:65px;">
		<div id="search">

			<?php echo form_open('skills/search');?>
			
			<input type="text" placeholder="Search..." name="search" size="30">
			<input id="btn" type='submit' value='Search'>
			<?php echo form_close();?>

		</div>
	</div>
</div>

<div class="row">

	<!-- Category Menu -->
  <div class="three columns tile" style="margin-left: 5px; width:20%;">
	  <div id="categories">
			<h1>Categories</h1>
			<?php echo "[$total_rows] Results Found in $query_time.";?>
			<?php foreach($categories as $category):?>
				<?php if($c_id===$category['category_id']): ?>
				  <a class="current-category" href="<?php echo site_url('/skills/categories/'.$category['category_id']);?>"><?php echo $category['name'];?></a><br>
				<?php else:?>
					<a href="<?php echo site_url('/skills/categories/'.$category['category_id']);?>"><?php echo $category['name'];?></a><br> 
				<?php endif;?>
			<?php endforeach;?>
	  </div>
	</div>

	<div class="nine columns tile" style="padding-bottom: 30px;">

		<!-- Skills output -->
		<?php if($no_results): ?>
  			<p>No Skills Match</p>
		<?php else:?>
				<?php $x=1;?>
				<?php foreach($skills as $skill):?>
					<div id="<?php echo 'skill-'.$x;?>" class="skill">
					  <div class="card">
							<img src="<?php echo $skill['picture']; ?>" alt="Skill pic" heigh="100" width="100">
							<h1><?php echo $skill['name'];?></h1>
							<p class="grey-heading"><?php  echo $skill['username'];?></p>
							<p><?php echo substr($skill['description'],0,50)."...";?></p>
							<p><button id="card-button" onclick="window.location.href='<?php echo site_url(); ?>skills/view/<?php echo $skill['skill_id']; ?>'">View</button></p>
						</div>
					</div>
					<?php $x++;?>
				<?php endforeach;?>
		<?php endif;?>
		
</div>
</div>
	<!-- Pagination output -->
<div class="row">
	<div class="pagination tile center">
	  <?php echo $this->pagination->create_links();?>
	</div>
</div>
