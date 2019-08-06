<div class="container tile standard-padding"> 

  <!--Title row -->
  <div class="row title-row">
    <div class="twelve columns">
        <h1>History</h1>   
        <p>Your last 10 Trades will appear here.</p>
    </div>
  </div>

  <div class="row">

    <!--Trades output table -->
    <table class="output-table u-full-width">
      <thead>
        <tr>
            <th>Your Skill</th>
            <th>Users Skill</th>
            <th>Their Username</th>
            <th>Date</th>
        </tr>
      </thead>

      <tbody>
        <?php if(!$no_results):?>
          <?php foreach($trades as $trade):?>
            <tr>
              <td><?php echo $trade['name']; ?></td>
              <td><?php echo $trade['other_name']; ?></td>
              <td><?php echo $trade['username']; ?></td>
              <td><?php echo $trade['completed_time']; ?></td>
            </tr>
          <?php endforeach;?>
        <?php else:?>
          <tr>
            <td>Sorry No Trades yet</tr>
          </tr>
        <?php endif;?>
      </tbody>
    </table><!--Close trades output table -->

  </div>
  
</div>