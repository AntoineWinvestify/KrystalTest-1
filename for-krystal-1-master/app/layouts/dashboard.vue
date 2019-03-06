<template>
	<v-app dark v-resize="onResize">
		<v-navigation-drawer
			:mini-variant="mini"
			v-model="drawer"
			:mobile-break-point="991"
			width=200
			fixed
			app
		>
			<template>
				<figure v-if="!mobile || menu" @click.stop="mini = !mini">
					<img v-if="!mini" src="~assets/img/logo-text.png" alt="">
					<img v-if="mini" src="~assets/img/logo.png" alt="">
				</figure>
				<figure v-else>
					<img v-if="!mini" src="~assets/img/logo-text.png" alt="">
					<img v-if="mini" src="~assets/img/logo.png" alt="">
				</figure>
			</template>
			<v-list dense>
				<v-list-tile nuxt :to="`/${lang}${item.href}`" v-for="(item, index) in options" :key="`${index}-${item.icon}`">
					<v-list-tile-action>
						<i :class="item.icon"></i>
					</v-list-tile-action>
					<v-list-tile-content>
						<v-list-tile-title>{{ item.name }}</v-list-tile-title>
					</v-list-tile-content>
				</v-list-tile>
			</v-list>
		</v-navigation-drawer>
		<v-toolbar color="#2D3136" height="80" clipped app absolute dark>
			<v-toolbar-side-icon v-if="menu" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
			<v-btn exact nuxt :to="back.link" v-if="back" @click="backEffect" :fab="clicked" dark flat>
				<v-icon dark>keyboard_arrow_left</v-icon>
				<span v-if="!clicked">Go Back</span>
			</v-btn>
			<v-spacer></v-spacer>
			<languages />
			<notifications />
			<span class="user-name">Lending Investment S.L</span>
			<v-btn icon>
				<v-icon>keyboard_arrow_down</v-icon>
			</v-btn>
		</v-toolbar>
		<v-content>
			<nuxt />
		</v-content>
		<v-dialog
			v-model="advertising"
			max-width="500px"
			transition="dialog-transition"
		>
			<v-card class="card-modal">
				<div class="title-modal-confirm">Advertising</div>
				<p>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum sit, eius odit enim repudiandae dolores animi optio porro ab tempore deleniti corporis nam totam blanditiis quidem officiis nulla voluptatem. Voluptatem.
				</p>
				<div class="buttons">
					<v-btn @click="closeModal">Cancel</v-btn>
					<v-btn class="btn-green" @click="closeModal">Ok</v-btn>
				</div>
			</v-card>
		</v-dialog>			
	</v-app>
</template>

<script>
	import Notifications from '~/components/ui/NotificationsList'
	import Languages from '~/components/ui/LanguagesList'
	export default {
		data () {
			return {
				drawer: true,
				mini: false,
				clicked: false,
				mobile: false,
				menu: false,
				options: [
					{ name: 'Dashboard', icon: 'icon-win-activity', href: '/dashboard' },
					{ name: 'Marketplace', icon: 'icon-win-bar-chart', href: '/marketplace' },
					{ name: 'Lending Registration', icon: 'icon-win-layers', href: '/lending-registration' },
					{ name: 'Link Account', icon: 'icon-win-zap', href: '/link' },
					{ name: 'Profile', icon: 'icon-win-user', href: '/profile' },
					{ name: 'Sign out', icon: 'icon-win-power', href: '/' }
				]
			}
		},
		computed: {
			back () {
				return this.$store.getters.back
			},
			advertising () {
				return this.$store.getters.advertisingModal
			},
			lang () {
				return this.$store.getters.locale
			}
		},
		methods: {
			backEffect () {
				this.clicked = !this.clicked
				setTimeout(() => {
					this.clicked = !this.clicked
				}, 1000)
			},
			onResize () {
				let x = window.innerWidth
				// let y = window.innerHeight
				this.mobile = false
				this.menu = false
				if (x > 990 && x <= 1264) {
					this.mini = true
					this.mobile = true
				} else if (x <= 991) {
					this.mobile = true
					this.menu = true
				}
			},
			closeModal () {
				this.$store.dispatch('advertisingModal', false)
			}
		},
		components: {
			Notifications,
			Languages
		}
	}
</script>
