<template>
	<v-menu content-class="notifications" offset-y>
		<v-btn slot="activator" icon>
			<v-badge overlap color="red darken-4">
				<span slot="badge">1</span>
				<v-icon>notifications</v-icon>
			</v-badge>			
		</v-btn>
		<v-flex xs12>
			<v-icon class="arrow-mark">arrow_drop_up</v-icon>
			<ul class="menu-options notification-menu">
				<li class="active">
					<span>Notification 1</span><span>Last week</span>
				</li>
				<li>
					<span>Notification 2</span><span>Last week</span>
				</li>
				<li>
					<span>Notification 3</span><span>Last week</span>
				</li>
				<li>
					<span>Notification 4</span><span>Last week</span>
				</li>												
			</ul>
		</v-flex>
	</v-menu>
</template>
<script>
	export default {
		data () {
			return {
			}
		}
	}
</script>