<div class="container tile standard-padding"> 

    <!--Title -->
    <div class="row title-row">
        <h1>My Offers</h1>
    </div>

    <!--Buttons -->
    <div id="listing-buttons">
      <input  class="listing-button" type="button" value="My Skills" onclick="window.location.href='<?php echo site_url(); ?>listings/view_skills'"/>
      <input  class="listing-button" type="button" value="Sent Offers" onclick="window.location.href='<?php echo site_url(); ?>listings/view_sent_offers'"/>
      <input  class="listing-button current"  type="button" value="My Offers" onclick="window.location.href='<?php echo site_url(); ?>listings/view_offers'"/>
    </div>

    <!--Output table -->
    <table class="output-table u-full-width">

      <!--Table head -->
      <thead>
        <tr>
          <th>Listing</th>
          <th>Status</th>
          <th>Offered Skill</th>
          <th>User</th>
        </tr>
      </thead>

      <!--Table body -->
      <tbody>
        <?php if(!$no_results):?>
          <?php foreach($skill_proposal as $proposal):?>
            <tr>
              <td><a class="skill-links" href="<?php echo site_url('/skills/view/'.$proposal['skill_id']);?>"><?php echo $proposal['name']; ?></a></td>
              <?php if ($proposal['status']!='pending approval'): ?>
                <td><a class="skill-links" href="<?php echo site_url('/trades/view_progress/'.$proposal['skill_proposal_id']);?>"><?php echo $proposal['status']; ?></a></td>
              <?php else: ?>
                <td><a class="skill-links" href="<?php echo site_url('/trades/decide/'.$proposal['skill_proposal_id']);?>"><?php echo $proposal['status']; ?></a></td>
              <?php endif; ?>
              <td><a class="skill-links" href="<?php echo site_url('/skills/view/'.$proposal['offer_skill_id']);?>"><?php echo $proposal['offered_skill']; ?></a></td>
              <td>
                <a class="skill-links" href="<?php echo site_url('/profiles/view/'.$proposal['user_id']);?>" >
                  <img class="thumb-img" src='<?php echo $gravatar_url; ?>'>
                  <?php echo $proposal['username']; ?></a>
              </td>
            </tr>
          <?php endforeach;?>
        <?php else:?>
          <tr>
            <td>Sorry No listings yet</tr>
          </tr>
         <?php endif;?>
      </tbody>

    </table> <!--Close table -->

</div><!--close container tile -->

<!--Pagination -->
<div class="row">
	<div class="pagination tile center">
	  <?php echo $this->pagination->create_links();?>
	</div>
</div>