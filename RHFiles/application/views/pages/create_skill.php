<div class="container tile standard-padding"> 

    <!-- Title -->
    <div class="row title-row">
        <h1>Create Skill</h1> 
    </div>

    <div><?php echo validation_errors(); ?></div>

    <!--Create skill Form -->
    <?php echo form_open('skills/create_skill');?>
    <div class="row" id="form-row">
        
            <div class="four columns">
               <label for="skillName">Skill Name</label>
               <input class="u-full-width" type="text" maxlength="15" placeholder="French Class" name="skillname">
            </div>

            <!--Score Listbox -->
            <div class="three columns">
                <label for="skillvalue">Value</label>
                 <select class="u-full-width" name="skillvalue">
                    <?php for($i=5;$i<=50;$i+=5): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor;?>
                 </select>    
            </div>

            <!-- Category listbox -->
            <?php $categories=$this->category_model->read_all_categories(); ?>
            <div class="four columns">
                <label for="exampleRecipientInput">Category</label>
                <select class="u-full-width" name="categoryid">
                  <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                  <?php endforeach; ?>
                </select>    
            </div>

    </div>

    <!-- Skill Description textbox -->
    <div class="row" id="form-row">
        <div class="twelve column" style="padding-right:5%;" >
          <label for="skilldesc">Skill Description</label>
          <textarea class="u-full-width" maxlength="253" placeholder="Learn Common french greetings..." name="skilldesc"></textarea>
        </div>
    </div>
     
    <!-- Submit button-->
    <div class="row">
        <input class="btn" type="submit" value="Submit">
    </div>
    <?php echo form_close(); ?>

</div>