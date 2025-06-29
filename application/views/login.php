<div class="login-page">
    <div class="login-body container">
        <div class="loginbox">
            <div class="login-right-wrap">
                <div class="account-header">
                    <div class="account-logo text-center mb-4">
                        <a href="<?php echo $base_url . "admin"; ?>">
                            <img src="<?php echo $base_url; ?>assets/img/mycommunity.png" alt="" class="img-fluid">
                        </a>
                    </div>
                </div>

                <!-- Login Header -->
                <div class="login-header">
                    <h3>Login <span>MyCommunity</span></h3>
                    <p class="text-muted">Access to our dashboard</p>
                </div>

                <?php if ($this->session->flashdata('error_message')) {  ?>
                    <div class="alert alert-danger text-center" id="flash_error_message"><?php echo $this->session->flashdata('error_message'); ?></div>
                    <?php $this->session->unset_userdata('error_message'); ?>
                <?php } ?>

                <?php if ($this->session->flashdata('success_message')) {  ?>
                    <div class="alert alert-success text-center" id="flash_succ_message"><?php echo $this->session->flashdata('success_message'); ?></div>
                    <?php $this->session->unset_userdata('success_message'); ?>
                <?php } ?>
                <form id="adminSignIn" action="<?php echo $base_url; ?>" method="POST">
                   <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>">
					<div class="form-group">
						<label class="control-label">Username</label>
						<input class="form-control" type="text" name="username" id="username" placeholder="Enter your username">
					</div>
					<div class="form-group mb-4">
						<label class="control-label">Password</label>
						<input class="form-control" type="password" name="password" id="password" placeholder="Enter your password">
					</div>
					
					<div class="form-group mb-4">
						<a href="<?php echo $base_url; ?>forgot_password" class="btn btn-info btn-sm" >Forgot Password?</a>
					</div>
					<div class="text-center">
						<button class="btn btn-primary btn-block account-btn" id="loginSubmit" type="button">Login</button>
					</div>
				</form>
                <!-- Keycloak Login Button -->
                <div class="text-center mt-2">
                    <a href="<?php echo base_url('oauth/login'); ?>" class="btn btn-primary btn-block account-btn">
                        Login with MyDigital ID
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>