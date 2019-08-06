<div class="container tile standard-padding"> 

  <!--Title -->
  <div class="row title-row">
    <h1>My Skills</h1>
  </div>

  <!--Buttons -->
  <div id="listing-buttons">
    <input  class="listing-button current" type="button" value="My Skills" onclick="window.location.href='<?php echo site_url(); ?>listings/view_skills'"/>
    <input  class="listing-button" type="button" value="Sent Offers" onclick="window.location.href='<?php echo site_url(); ?>listings/view_sent_offers'"/>
    <input  class="listing-button" type="button" value="My Offers" onclick="window.location.href='<?php echo site_url(); ?>listings/view_offers'"/>
  </div>

  <!--Output table -->
  <table class="output-table u-full-width">

    <!--Table head -->
    <thead>
      <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Value</th>
        <th>Status</th>
      </tr>
    </thead>

    <!--Table body -->
    <tbody>

      <?php if(!$no_results):?>
        <?php foreach($user_skills as $skill):?>
          <tr>
            <td><a class="skill-links" href="<?php echo site_url('/skills/view/'.$skill['skill_id']);?>"><?php echo $skill['name']; ?></a></td>
            <td><?php echo substr($skill['description'],0,32).'...'; ?></td>
            <td style="font-weight:bolder;"><?php echo $skill['value']; ?></td>
            <td id="active">ACTIVE</td>
          </tr>
        <?php endforeach;?>
      <?php else:?>
        <tr>
          <td>Sorry No listings yet</tr>
        </tr>
      <?php endif; ?>
    
    </tbody>

  </table> <!--Close Table  -->

</div><!--Close container tile -->

<div class="row">
	<div class="pagination tile center">
	  <?php echo $this->pagination->create_links();?>
	</div>
</div>