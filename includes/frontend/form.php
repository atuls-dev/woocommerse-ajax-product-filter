<?php
global $custom_course_finder;

?>
<style>
#myProgress {
  width: 100%;
  background-color: #fff;
} 

#myBar {
  width: 1%;
  height: 30px;
  background-color: #9C41DF;
}

</style>
<section class="signup-step-container">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="columns">
                    <div class="wizard">
                        <form role="form" id="courseFinderForm" data-skipStep="" action="index.html" class="login-box">
                            <input type="hidden" name="action" value="cf_submit_form" >
                            <div class="tab-content" id="main_form">
                                <div class="tab-pane tabPane activeStep" role="tabpanel" id="step1" data-tab="1" >
                                    <h4 class="text-center">Wie alt bist du?</h4>
                                    <div class="row">
                                      <span id="alterText"><?= $custom_course_finder->get_range()[0] ?></span>
                                      <input type="hidden" id="alter" name="alter" value="<?= $custom_course_finder->get_range()[0] ?>" >
                                      <input type="range" id="alterRange" name="alterRange" value="0" min="0" max="<?=count( $custom_course_finder->get_range() )-1?>" steps="1" data-alter="" >
                                    </div>
                                </div>

                                <div class="tab-pane tabPane" role="tabpanel" id="step2" data-tab="2" >
                                    <h4 class="text-center">Welchen Tanzstil möchtest du tanzen</h4>
                                    <div class="row"> 
                                      <div class="middle tabContent2">
                                        <!--  
                                        <label>
                                          <input type="radio" name="radio">
                                          <div class="front-end box">
                                            <span>Ballett</span>
                                          </div>
                                        </label>

                                         <label>
                                            <input type="radio" name="radio"/>
                                            <div class="back-end box">
                                              <span>Jazz</span>
                                            </div>
                                          </label>

                                          <label>
                                            <input type="radio" name="radio"/>
                                            <div class="back-end box">
                                              <span>Hip Hop</span>
                                            </div>
                                        </label>

                                        <label>
                                          <input type="radio" name="radio"/>
                                            <div class="back-end box">
                                            <span>Stage Arts</span>
                                          </div>
                                        </label> 
                                        -->
                                      </div>
                                   </div>
                                </div>


                                <div class="tab-pane tabPane" role="tabpanel" id="step3" data-tab="3">
                                   <h4 class="text-center">Welchen Vorkenntnisse hast du?</h4>
                                    <div class="row"> 
                                      <div class="middle steptwo tabContent3">
                                        <!--  <label>
                                            <input type="radio" name="radio">
                                            <div class="box">
                                              <span>Keine, ich möchte einsteigen</span>
                                            </div>
                                          </label>

                                         <label>
                                          <input type="radio" name="radio"/>
                                          <div class=" box">
                                            <span>ich hatte schon Unterricht</span>
                                          </div>
                                        </label>

                                        <label>
                                        <input type="radio" name="radio"/>
                                        <div class="box">
                                          <span>Ich bin schon ziemlich gut </span>
                                        </div>
                                      </label> -->
                                    </div>
                                   </div>
                                   
                                </div>

                                <div class="tab-pane tabPane" role="tabpanel" id="step4" data-tab="4">
                                    <h4 class="text-center">Dein Kurs:</h4>
                                    <div class="row"> 
                                      <div class="middle stepthree tabContent4">
                                         <!-- <label>
                                            <input type="radio" name="radio">
                                            <div class="box">
                                              <span>Jazz 1 Donnerstag <br>16:00 Uhr</span>
                                            </div>
                                        </label>

                                        <label>
                                          <input type="radio" name="radio"/>
                                          <div class="box">
                                            <span>Alternativtermin</span>
                                          </div>
                                        </label> -->
                                      </div>
                                    </div>
                                    
                                </div>

                                <div class="tab-pane tabPane" role="tabpanel" id="step5" data-tab="5">
                                    <h4 class="text-center">Deine unverbindliche, kostenlose Onlinebuchung</h4>
                                    <div class="row"> 
                                      <div class="middle">
                                     <div class="form-new">
                                      <h3>Targen sie jetzt lhre kontaktdaten ein:  </h3>
                                      <div class="input-group mb-3">
                                        <label>lhr Name:*</label>  
                                        <div class="input-group-prepend">
                                          <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="user_name" placeholder="lhr Name...">
                                      </div>

                                      <div class="input-group mb-3">
                                          <label>lhr Email Adresse:*</label>
                                        <div class="input-group-prepend">
                                          <span class="input-group-text"><i class="far fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="user_email" placeholder="lhr Email Adresse...">
                                      </div>

                                       <div class="input-group mb-3">
                                          <label>lhr Telefonnummer:*</label>
                                          <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                          </div>
                                        <input type="text" class="form-control" name="user_telephone" placeholder="lhr Telefonnummer...">
                                      </div>
                                      <div class="form-group text-center mar-b-0">
                                          <input type="submit" id="cfSubmit" name="submit" value="Nachricht absenden!" class="btn btn-primary next sub-btn">        
                                      </div>
                                  </div>
                                     
                                   </div>
                                </div>
                              </div>

                             <!--  <div class="tab-pane tabPane" role="tabpanel" id="step6" data-tab="6">
                                <h4 class="text-center">Sichern Sie sich eine kostenfreie Bedarfsanalyse lhrer
                                korperlichen Fitness:</h4>
                                <div class="row"> 
                                  <div class="middle">
                                     <div class="form-new">
                                      <h3>Was mochthen Sie primar erreichen?  </h3>
                                      <div class="steps-icons">
                                        <div class="flex-icons">
                                          <img src="<?= CFC ?>assets/images/icon1.png">
                                          <span>Abnehmen</span>
                                        </div>
                                        <div class="flex-icons">
                                          <img src="<?= CFC ?>assets/images/step2.png">
                                           <span>Muskelaufbau</span>
                                        </div>
                                        <div class="flex-icons">
                                          <img src="<?= CFC ?>assets/images/step3.png">
                                           <span>kondition verbessern</span>
                                        </div>
                                      </div>
                                    </div> 
                                  </div>
                                </div>
                              </div> -->

                              <div class="row progressBar" >
                                <button type="button" id="backStep" ><img src="<?= CFC ?>assets/images/leftarrow.png"></button>
                                <div id="myProgress">
                                  <div id="myBar"></div>
                                </div>
                                <button type="button" id="nextStep" ><img src="<?= CFC ?>assets/images/rightarrow.png"></button>
                              </div>

                               <!--  <ul class="progressbar">
                                      <div class="progress"></div>
                                        <li class="pull-left default-btn prev-step"><img src="<?= CFC ?>assets/images/leftarrow.png"></li>
                                        <li class="pull-right default-btn next-step"><img src="<?= CFC ?>assets/images/rightarrow.png"></li>
                                </ul> -->
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>