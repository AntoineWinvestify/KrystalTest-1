<template>
	<div :class="{ selected }" class="registration-item" xs4>
		<figure>
			<img :src="lending && lending.company_logo_url ? lending.company_logo_url : '/lendings/mintos_logo.png'" alt="">
		</figure>
		<div class="policies custom-checks">
			<v-checkbox
				v-model="policy"
				color="light-green accent-3"
				value=""
				@change="checksTerms"
				hide-details
			>
				<template slot="label">
					<div class="label-check">
						I have Read and Accept their <a :href="lending.company_privacy_url" target="_blank">Privacy Policy</a>
					</div>
				</template>
			</v-checkbox>
			<v-checkbox
				v-model="terms"
				color="light-green accent-3"
				value=""
				@change="checksTerms"
				hide-details
			>
				<template slot="label">
					<div class="label-check">
						I have Read and Accept the <a :href="lending.company_termsofservice_url" target="_blank">Terms and Conditions of Use</a>
					</div>
				</template>								
			</v-checkbox>								
		</div>
		<div class="actions-btns">
			<v-btn v-if="!selected" @click="selected = !selected" :disabled="!allowSelect" class="only-one" block >Select</v-btn>
			<div v-else-if="allowSelect && selected" class="both">
				<v-btn @click="cancelLending" >Cancel</v-btn>
				<v-btn class="selected" >Selected</v-btn>
			</div>
		</div>
	</div>	
</template>
<script>
	export default {
		props: {
			lending: {
				type: Object,
				default: () => {
					return {}
				}
			}
		},
		data () {
			return {
				policy: false,
				terms: false,
				allowSelect: false,
				selected: false
			}
		},
		mounted () {

		},
		methods: {
			checksTerms () {
				if (this.policy && this.terms) {
					this.allowSelect = true
				} else {
					this.selected = false
					this.allowSelect = false
				}
			},
			cancelLending () {
				this.policy = false
				this.terms = false
				this.selected = false
				this.allowSelect = false
			}
		}
	}
</script>