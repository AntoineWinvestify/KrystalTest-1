<template>
	<div v-resize="onResize">
		<article class="landing landing-step-1">
			<v-container>
				<header>
					<nav v-if="!mobile">
						<ul>
							<li>Home</li>
							<li>Solutions</li>
							<li>Platforms</li>
							<li>About Us</li>
							<li>Contact us</li>
						</ul>
					</nav>
					<v-icon v-else @click="drawer = true">menu</v-icon>
					<div class="actions">
						<v-menu left @click="menu = !menu" v-model="menu" offset-y :close-on-content-click="false">
							<v-btn slot="activator" color="white" flat>Log In</v-btn>
							<v-form ref="login" v-model="valid" class="login-form custom-form">
								<v-text-field :rules="validation.usernameRules" name="username" label="Username"></v-text-field>
								<v-text-field :rules="validation.passwordRules" name="password" label="Password" type="password"></v-text-field>
								<div class="login-button">
									<v-btn :disabled="!valid" color="white" nuxt :to="`/${$store.state.locale}/dashboard`">Log in</v-btn>
								</div>
							</v-form>
						</v-menu>						
						<v-btn @click="showModalRegister" class="register-button">Register</v-btn>
					</div>
				</header>
				<v-navigation-drawer
					v-model="drawer"
					width=200
					fixed
					app
					class="mobile-menu"
					v-if="mobile"
				>
					<figure>
						<img src="~assets/img/logo-text.png" alt="">
					</figure>
					<nav>
						<ul>
							<li>Home</li>
							<li>Solutions</li>
							<li>Platforms</li>
							<li>About Us</li>
							<li>Contact us</li>
						</ul>
					</nav>
				</v-navigation-drawer>
				<section class="landing-step-1-container">
					<v-layout column wrap>
						<v-flex class="first-line" xs12>
							Intelligent Solution for Peer to Peer Lending Investors
						</v-flex>
						<v-flex class="second-line" xs12>
							Monitor  all your online lending platforms accounts in one place.
						</v-flex>
						<v-flex xs12>
							<v-btn class="button-open-account">Open Account</v-btn>
						</v-flex>				
					</v-layout>
				</section>
				<section>
					<v-layout column wrap>
						<v-flex class="platforms-list" xs12>
							<div class="platforms-list-content">
								<figure>
									<img src="~/assets/img/lendings/bondora_logo.png" alt="">
								</figure>
								<figure>
									<img src="~/assets/img/lendings/finanzarel_logo_white.png" alt="">
								</figure>
								<figure>
									<img src="~/assets/img/lendings/mintos_logo.png" alt="">
								</figure>
								<figure>
									<img src="~/assets/img/lendings/twino.png" alt="">
								</figure>
								<figure>
									<img src="~/assets/img/lendings/loanbook_logo.png" alt="">
								</figure>
							</div>
						</v-flex>
						<v-flex class="third-line" xs12>
							Connect to the most influential P2P Lending Platforms in Europe
						</v-flex>
					</v-layout>
				</section>
			</v-container>
		</article>
		<article class="landing landing-step-2">
			<v-container grid-list-xs>
				<div class="logo-container">
					<figure>
						<img src="~/assets/img/logo-text.png" alt="">
					</figure>
				</div>
				<div class="first-line">
					We empower our investors with software and data analytics solutions that provide data-driven insights regarding their P2P loans.
				</div>
				<v-layout row wrap>
					<v-flex xs12>
						<v-layout row wrap>
							<v-flex xs12 sm5 md4 lg3>
								<div class="info-item">
									<figure>
										<img src="~/assets/img/landing/laptop_1.png" alt="">
									</figure>
									<span class="info-item-title">Data Aggregate</span>
									<p>Standardize and reconcile all your financial information in one place</p>
								</div>
							</v-flex>
							<v-flex xs12 sm6 md5 lg4>
								<div class="info-item">
									<figure>
										<img src="~/assets/img/landing/analysis.png" alt="">
									</figure>
									<span class="info-item-title">Simply Management</span>
									<p>Connect their data to our dashboard for monitoring your accounts and measure your  investments performance</p>
								</div>
							</v-flex>							
						</v-layout>
					</v-flex>
					<v-flex xs12>
						<v-layout row wrap>
							<v-flex xs12 sm5 md4 lg3>
								<div class="info-item">
									<figure>
										<img src="~/assets/img/landing/worldwide.png" alt="">
									</figure>
									<span class="info-item-title">Market Analysis</span>
									<p>Access high-quality data, to help you benchmark performance and evaluate your lending platforms </p>
								</div>
							</v-flex>
							<v-flex xs12 sm6 md5 lg4>
								<div class="info-item">
									<figure>
										<img src="~/assets/img/landing/analytics.png" alt="">
									</figure>
									<span class="info-item-title">Portfolio Composition</span>
									<p>Quickly analyze composition and performance across any level of your portfolio to identify opportunities and better evaluate your risk.</p>
								</div>
							</v-flex>							
						</v-layout>
					</v-flex>					
				</v-layout>
			</v-container>
		</article>
		<v-parallax :src="'/jassim-vailoces-371831-unsplash.jpg'">
			<article class="landing-step-3">
				<v-layout class="landing landing-step-3-container" column wrap>
						<v-flex class="first-line" xs12>
							Safely Navigate  the complexities of the Online Lending Market
						</v-flex>
						<v-flex class="second-line" xs12>
							<p>Winvestify is at the forefront of this opportunity with analytics solutions and expert services that help our investors navigate the complexities of the Online Lending market to maximize the value of their assets and portfolios.</p>
							<p>We are touching every stage of the lifecycle of P2P loans which gives us a significant advantage and access to robust market data.</p>
							<p>At Winvestify we are providing transparency and insight into every aspect of the Online Lending Market</p>
						</v-flex>
						<v-flex xs12>
							<v-btn class="button-open-account">Open Account</v-btn>
						</v-flex>				
				</v-layout>
			</article>
		</v-parallax>
		<article class="landing landing-step-4">
			<v-layout row wrap>
				<v-flex class="landing-step-4-title" xs12>
					All Your Investment Data Standardized
				</v-flex>
				<v-flex class="info-item-list" xs12>
					<v-layout row wrap>
						<v-flex xs12 class="item-info" sm4 md3 offset-md1 lg2 offset-lg2>
							<figure>
								<img src="~/assets/img/landing/analytics_4.png" alt="">
							</figure>
							<span class="item-info-title">Portfolio Data</span>
							<p>Winvestify has direct, automated feeds from all major European online lenders, allowing us to display, in real-time, your loan portfolio.</p>
						</v-flex>
						<v-flex xs12 class="item-info" sm4 md4 lg2 offset-lg1>
							<figure>
								<img src="~/assets/img/landing/man.png" alt="">
							</figure>
							<span class="item-info-title">Normalization</span>
							<p>Winvestify has established universal data formats across all credit verticals, allowing consistent views of loans purchased across all major lending platforms.</p>
						</v-flex>
						<v-flex xs12 class="item-info" sm4 md3 lg2 offset-lg1>
							<figure>
								<img src="~/assets/img/landing/analytics_3.png" alt="">
							</figure>
							<span class="item-info-title">Transformation</span>
							<p>We run platform-specific data cleaning rules to ensure that our clients always access the highest quality data possible.</p>
						</v-flex>												
					</v-layout>
				</v-flex>
			</v-layout>
		</article>
		<footer>
			<v-container >
				<v-layout row wrap>
					<v-flex xs12 sm2 offset-sm1 md2 offset-md1 lg2 offset-lg1>
						<ul>
							<li>Index</li>
							<li>About us</li>
							<li>Investors</li>
							<li>Platforms</li>
							<li>Team</li>
						</ul>
					</v-flex>
					<v-flex xs12 sm2 md2 lg2>
						<ul>
							<li>Recent Posts</li>
							<li>About Crowdlending</li>
							<li>Risk P2P Lending </li>
							<li>Winvestify Tools</li>
							<li>Top Lending Platforms</li>
						</ul>
					</v-flex>
					<v-flex xs12 sm2 md2 lg2>
						<ul>
							<li>RRSS</li>
							<li>LinkedIn</li>
							<li>Twitter</li>
							<li>Facebook</li>
							<li>YouTube</li>
						</ul>
					</v-flex>
					<v-flex xs12 sm2 md2 lg2>
						<ul class="opacity-list">
							<li>SUPPORT</li>
							<li>Terms & Conditions</li>
							<li>Privacy policy</li>
							<li>Contact Us</li>
							<li>FAQ</li>
						</ul>
					</v-flex>
					<v-flex xs12 sm3 md3 lg2>
						<div class="footer-message">
							<figure>
								<img src="~/assets/img/logo-text.png" alt="">
							</figure>
							<p>Winvestify is a technology solution that help our clients navigate the complexities of the Online Lending Market</p>
						</div>
					</v-flex>
				</v-layout>
			</v-container>
		</footer>
		<v-dialog
			v-model="dialogRegister"
			:overlay="false"
			max-width="600px"
			transition="dialog-transition"
		>
			<div class="form-register-content">
				<figure>
					<img src="~/assets/img/logo-text.png" alt="">
				</figure>
				<v-form ref="formStep1" v-model="validStep1" v-show="step === 1">
					<v-flex xs12>
						<v-text-field v-model="user.email" :rules="validation.emailRules" label="Email"></v-text-field>
					</v-flex>
					<v-flex xs12>
						<v-text-field type="password" v-model="user.password" :rules="validation.passwordRules" label="Password"></v-text-field>
					</v-flex>
					<v-flex xs12>
						<v-text-field type="password" v-model="user.rePassword" :rules="validation.rePassRules" label="Repeat Password"></v-text-field>
					</v-flex>
					<v-flex xs12>
						<v-text-field v-model="user.phone" :rules="validation.mobileRules" persistent-hint label="Mobile phone"
						hint="For security reasons it is mandatory to include your mobile phone number so we can send you an account activation code"></v-text-field>
					</v-flex>
					<v-flex class="custom-checks register" xs12>
						<v-checkbox
							v-model="terms"
							label="Business Loans"
							color="light-green accent-3"
							value=""
							:rules="validation.termsRules"
							hide-details
						>
							<template slot="label">
								I accept the <a href="#">terms of service</a> and the <a href="#">privacy policy</a>
							</template>
						</v-checkbox>
						<v-flex xs12 text-xs-right>
							<v-btn :disabled="!validStep1" @click="step = 2" class="next-button">Next</v-btn>
						</v-flex>
					</v-flex>
				</v-form>
				<v-form ref="formStep2" class="step-2" v-model="validStep2" v-show="step === 2">
					<v-flex class="message" xs12>
						<p>Dear Investor,</p>
						<p>A confirmation code has been sent to {{ user.phone }}</p>
					</v-flex>
					<v-flex xs12>
						<v-text-field v-model="user.code" :rules="validation.codeRules" label="Confirmation Code"></v-text-field>
					</v-flex>
					<v-flex xs12 mt-5>
						You did not receive a code?
					</v-flex>
					<v-flex class="buttons" xs12>
						<v-btn color="blue-grey darken-1" class="new-code">Request new code</v-btn>
						<v-btn :disabled="!validStep2" @click="step = 3" class="next-button">Next</v-btn>
					</v-flex>
				</v-form>
				<v-form ref="formStep3" class="step-3" v-model="validStep3" v-show="step === 3">
					<v-flex class="message" text-xs-center xs12>
						<p>Your Account Has Been Successfully Created</p>
						<p>For security reasons it is mandatory to include your name and surnames for the correct identification of your account</p>
					</v-flex>
					<v-flex xs12>
						<v-text-field v-model="user.name" :rules="validation.nameRules" label="Name"></v-text-field>
					</v-flex>
					<v-flex xs12>
						<v-text-field v-model="user.surnames" :rules="validation.surnamesRules" label="Surnames(s)"></v-text-field>
					</v-flex>					
					<v-flex xs12 mt-5>
						Are you an accredited investor?
					</v-flex>
					<v-flex xs12>
						<v-layout row wrap>
							<v-flex class="custom-checks main" xs12>
								<v-checkbox
									v-model="ex1"
									label="Yes"
									color="light-green accent-3"
									value="yes"
									:rules="validation.investorRules"
									hide-details
								></v-checkbox>
								<v-checkbox
									v-model="ex1"
									label="No"
									color="light-green accent-3"
									value="no"
									:rules="validation.investorRules"
									hide-details
								></v-checkbox>
							</v-flex>
						</v-layout>
					</v-flex>
					<v-flex text-xs-right xs12>
						<v-btn :disabled="!validStep3" @click="dialogRegister = !dialogRegister, clear" class="next-button">Start</v-btn>
					</v-flex>
				</v-form>				
			</div>
		</v-dialog>
	</div>
</template>
<script>
	export default {
		head () {
			return {
				title: 'Winvestify - Home'
			}
		},
		data () {
			return {
				value: '',
				ex1: false,
				step: 1,
				validStep1: false,
				validStep2: false,
				validStep3: false,
				user: {},
				terms: false,
				dialogRegister: false,
				menu: false,
				valid: false,
				mobile: false,
				drawer: false,
				items: [
					{ title: 'Click Me' },
					{ title: 'Click Me' },
					{ title: 'Click Me' },
					{ title: 'Click Me 2' }
				],
				validation: {
					passwordRules: [
						v => !!v || 'Password is required',
						v => (v && v.length >= 5) || 'Password must be more than 5 characters'
					],
					rePassRules: [
						v => !!v || 'Re-password is required',
						v => (v === this.user.password) || 'Password and re-password field must be the same'
					],
					emailRules: [
						v => !!v || 'E-mail is required',
						v => /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail must be valid'
					],
					mobileRules: [
						v => !!v || 'Mobile phone is required',
						v => (v && v.length >= 5) || 'Mobile phone must be more than 5 characters'
					],
					termsRules: [
						v => !!v || 'You must accept our terms of service and the privacy policy'
					],
					codeRules: [
						v => !!v || 'Confirmation Code is required',
						v => (v && v.length >= 5) || 'Confirmation Code must be more than 5 characters'
					],
					nameRules: [
						v => !!v || 'Name is required',
						v => (v && v.length >= 5) || 'Name must be more than 5 characters'
					],
					surnamesRules: [
						v => !!v || 'Surname(s) is required',
						v => (v && v.length >= 5) || 'Surname(s) must be more than 5 characters'
					],
					investorRules: [
						v => !!v || 'You must accept our terms of service and the privacy policy'
					]
				}
			}
		},
		methods: {
			showModalRegister () {
				this.clear()
				this.dialogRegister = true
			},
			clear () {
				this.$refs.formStep1.reset()
				this.$refs.formStep2.reset()
				this.$refs.formStep3.reset()
				this.step = 1
			},
			onResize () {
				let x = window.innerWidth
				this.mobile = false
				if (x <= 960) {
					this.mobile = true
				}
			}
		},
		layout: 'landing'
	}
</script>
