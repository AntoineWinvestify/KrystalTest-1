<template>
	<v-menu content-class="langs" offset-y>
		<v-btn slot="activator" icon>
			<v-icon>language</v-icon>
		</v-btn>
		<v-flex xs12>
			<v-icon class="arrow-mark">arrow_drop_up</v-icon>
			<ul class="menu-options langs-menu">
				<li @click="getLink(item.name.toLowerCase())" :class="{ 'active': $store.state.locale === item.name.toLowerCase() }" v-for="(item, index) in langs" :key="index">
					<img :src="item.flag" :alt="item.name"> {{ item.name }}
				</li>										
			</ul>
		</v-flex>
	</v-menu>
</template>
<script>
	export default {
		data () {
			return {
				langs: [
					{ name: 'EN', flag: '/united-states-of-america-flag.png' },
					{ name: 'ES', flag: '/spain-flag.png' }
				]
			}
		},
		methods: {
			getLink (lang) {
				console.log(this.$route)
				const { matched, params } = this.$route
				this.$router.push({
					name: matched.name,
					params: {
						lang: lang,
						id: params.id
					}
				})
			}
		}
	}
</script>