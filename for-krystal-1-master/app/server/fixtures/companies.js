export const companies = [
	{
		'id': '6',
		'company_name': 'myTripleAAA',
		'company_url': 'www.mytripleaaa.com',
		'company_country': 'ES',
		'company_country_name': 'SPAIN',
		'company_privacy_url': 'https://testing4.azarus.es/pages/privacyPolicy',
		'company_termsofservice_url': 'https://testing4.azarus.es/pages/termsOfService',
		'company_logo_url': 'https://testing4.azarus.es/img/logo/MyTripleA.png'
	},
	{
		'id': '2',
		'company_name': 'Comunitae',
		'company_url': 'www.comunitae.es',
		'company_country': 'ES',
		'company_country_name': 'SPAIN',
		'company_privacy_url': 'https://testing4.azarus.es/pages/privacyPolicy',
		'company_termsofservice_url': 'https://testing4.azarus.es/pages/termsOfService',
		'company_logo_url': 'https://testing4.azarus.es/img/logo/Comunitae.png'
	},
	{
		'id': '4',
		'company_name': 'Arboribus',
		'company_url': 'www.arboribus.com',
		'company_country': 'ES',
		'company_country_name': 'SPAIN',
		'company_privacy_url': 'https://testing4.azarus.es/pages/privacyPolicy',
		'company_termsofservice_url': 'https://testing4.azarus.es/pages/termsOfService',
		'company_logo_url': 'https://testing4.azarus.es/img/logo/Arboribus.png'
	},
	{
		'id': '7',
		'company_name': 'Lendix',
		'company_url': 'www.lendix.com',
		'company_country': 'ES',
		'ccompany_country_name': 'SPAIN',
		'company_privacy_url': 'https://testing4.azarus.es/pages/privacyPolicy',
		'company_termsofservice_url': 'https://testing4.azarus.es/pages/termsOfService',
		'company_logo_url': 'https://testing4.azarus.es/img/logo/Lendix.png'
	}
]

export const marketplace = [
	{
		'company_id': 25,
		'company_logo_url': 'https://testing4.azarus.es/img/logo/Mintos.png',
		'company_key_info': {
			'company_key_info_text': 'We provide retail investors with an easy ...',
			'company_name': 'Mintos',
			'company_number_of_investors': 43342,
			'company_start': '2013',
			'company_country_of_origin': 'Latvia',
			'company_total_invested_assets': {
				'amount': {
					'value': 2342443223,
					'currency_code': 'EUR'
				}
			}
		},
		'company_product_details': {
			'loan_types': 'All Loans',
			'minimum_investment': {
				'value': 10,
				'currency_code': 'EUR'
			},
			'autoinvest_option': true,
			'fees': true,
			'secondary_market': false,
			'supported_currencies': [
				{
					'currency_code': 'EUR'
				},
				{
					'currency_code': 'USD'
				}
			],
			'lending_return': 2,
			'buyback_guarantee': true,
			'collateral': false,
			'crossborder': true
		},
		'company_winvestify_evaluation': {
			'global_evaluation_actual_value': 800,
			'global_evaluation_max': 1000,
			'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP'
		}
	},
	{
		'company_id': 26,
		'company_logo_url': 'https://testing4.azarus.es/img/logo/Finanzarel.png',
		'company_key_info': {
			'company_key_info_text': 'We provide retail investors with an easy ...',
			'company_name': 'Finanzarel',
			'company_number_of_investors': 3342,
			'company_start': '2013',
			'company_country_of_origin': 'Spain',
			'company_total_invested_assets': {
				'amount': {
					'value': 24244228,
					'currency_code': 'EUR'
				}
			}
		},
		'company_product_details': {
			'loan_types': 'All Loans',
			'minimum_investment': {
				'value': 10,
				'currency_code': 'EUR'
			},
			'autoinvest_option': true,
			'fees': false,
			'secondary_market': false,
			'supported_currencies': [
				{
					'currency_code': 'EUR'
				},
				{
					'currency_code': 'USD'
				}
			],
			'lending_return': 2,
			'buyback_guarantee': true,
			'collateral': false,
			'crossborder': true
		},
		'company_winvestify_evaluation': {
			'global_evaluation_actual_value': 740,
			'global_evaluation_max': 1000,
			'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP'
		}
	},
	{
		'company_id': 7,
		'company_logo_url': 'https://testing4.azarus.es/img/logo/Loanbook.png',
		'company_key_info': {
			'company_key_info_text': 'We provide retail investors with an easy ...',
			'company_name': 'Loanbook',
			'company_number_of_investors': 442,
			'company_start': '2013',
			'company_country_of_origin': 'Spain',
			'company_total_invested_assets': {
				'amount': {
					'value': 23443223,
					'currency_code': 'EUR'
				}
			}
		},
		'company_product_details': {
			'loan_types': 'All Loans',
			'minimum_investment': {
				'value': 10,
				'currency_code': 'EUR'
			},
			'autoinvest_option': true,
			'fees': false,
			'secondary_market': false,
			'supported_currencies': [
				{
					'currency_code': 'EUR'
				},
				{
					'currency_code': 'USD'
				}
			],
			'lending_return': 2,
			'buyback_guarantee': true,
			'collateral': false,
			'crossborder': true
		},
		'company_winvestify_evaluation': {
			'global_evaluation_actual_value': 540,
			'global_evaluation_max': 1000,
			'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP'
		}
	}
]

export const getCompany = (id) => {
	return marketplaceDetail.find(item => {
		return item.company_id == id
	})
}

export const marketplaceDetail = [
	{
		'company_id': 26,
		'company_name': 'Twino',
		'company_logo_url': 'https://testing4.azarus.es/img/logo/Twino.png',
		'company_key_info': {
			'company_key_info_text': 'We provide retail investors with an easy ...',
			'company_name': 'Twino',
			'company_number_of_investors': 12045,
			'company_start': '2014',
			'company_country_of_origin': 'Latvia',
			'company_total_invested_assets': {
				'amount': {
					'value': 72443223,
					'currency_code': 'EUR'
				}
			}
		},
		'company_product_details': {
			'loan_types': 'All Loans',
			'minimum_investment': {
				'value': 10,
				'currency_code': 'EUR'
			},
			'autoinvest_option': true,
			'fees': false,
			'secondary_market': false,
			'supported_currencies': [
				{
					'currency_code': 'EUR'
				},
				{
					'currency_code': 'GBP'
				}
			],
			'lending_return': 2,
			'buyback_guarantee': true,
			'collateral': false,
			'is_regulated': true,
			'crossborder': true
		},
		'company_extendedInfo': {
			'custodial_funds': 'Payasera',
			'transfer_funds': 'SEPA Bank Transfer',
			'withdrawals_fee': false,
			'secondary_market_fee': false,
			'sell_outs': false,
			'accepted_investors': 'Worldwide',
			'taxation': 'Country of Origin'
		},
		'company_commercial_offer_info': {
			'commercial_info_text': 'You will receive 5€ ....',
			'commercial_offer_url': 'https://www.twino.eu'
		},
		'company_winvestify_evaluation': {
			'global_evaluation_actual_value': 810,
			'global_evaluation_max': 1000,
			'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP',
			'details': [
				{
					'display_name': 'Transparency',
					'value': 74
				},
				{
					'display_name': 'Investor Protection',
					'value': 64,
					'tooltip_display_name': 'Nothing defined in protection'
				},
				{
					'display_name': 'Technology',
					'value': 62,
					'tooltip_display_name': 'Nothing defined yet in technology'
				},
				{
					'display_name': 'Customer Care',
					'value': 86
				},
				{
					'display_name': 'Risk Scoring',
					'value': 93,
					'tooltip_display_name': 'Nothing defined'
				},
				{
					'display_name': 'Regulated',
					'value': 33,
					'tooltip_display_name': 'Nothing defined in regulated'
				},
				{
					'display_name': 'Time In Market',
					'value': 66,
					'tooltip_display_name': 'Nothing defined in market'
				},
				{
					'display_name': 'Team',
					'value': 78
				},
				{
					'display_name': 'Usability',
					'value': 83,
					'tooltip_display_name': 'No tooltip contents defined'
				},
				{
					'display_name': 'Yield',
					'value': 43,
					'tooltip_display_name': 'No tooltip contents defined for yield'
				}
			]
		}
	},
	{
		'company_id': 7,
		'company_name': 'Loanbook',
		'company_logo_url': 'https://testing4.azarus.es/img/logo/Loanbook.png',
		'company_key_info': {
			'company_key_info_text': 'We provide retail investors with an easy ...',
			'company_name': 'Loanbook',
			'company_number_of_investors': 2342,
			'company_start': '2014',
			'company_country_of_origin': 'Spain',
			'company_total_invested_assets': {
				'amount': {
					'value': 7443223,
					'currency_code': 'EUR'
				}
			}
		},
		'company_product_details': {
			'loan_types': 'P2B loans',
			'minimum_investment': {
				'value': 60,
				'currency_code': 'EUR'
			},
			'autoinvest_option': false,
			'fees': true,
			'secondary_market': false,
			'supported_currencies': [
				{
					'currency_code': 'EUR'
				}
			],
			'lending_return': 2,
			'buyback_guarantee': false,
			'collateral': false,
			'is_regulated': true,
			'crossborder': true
		},
		'company_extendedInfo': {
			'transfer_funds': 'SEPA Bank Transfer',
			'withdrawals_fee': false,
			'secondary_market_fee': false,
			'sell_outs': false,
			'accepted_investors': 'Spain Only',
			'taxation': 'Country of Origin'
		},
		'company_commercial_offer_info': {
			'commercial_info_text': 'You will receive 5€ ....',
			'commercial_offer_url': 'https://www.loanbook.es'
		},
		'company_winvestify_evaluation': {
			'global_evaluation_actual_value': 620,
			'global_evaluation_max': 1000,
			'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP',
			'details': [
				{
					'display_name': 'Transparency',
					'value': 5
				},
				{
					'display_name': 'Investor Protection',
					'value': 44,
					'tooltip_display_name': 'Nothing defined in protection'
				},
				{
					'display_name': 'Technology',
					'value': 44,
					'tooltip_display_name': 'Nothing defined yet in technology'
				},
				{
					'display_name': 'Customer Care',
					'value': 86
				},
				{
					'display_name': 'Risk Scoring',
					'value': 93,
					'tooltip_display_name': 'Nothing defined'
				},
				{
					'display_name': 'Regulated',
					'value': 88,
					'tooltip_display_name': 'Nothing defined in regulated'
				},
				{
					'display_name': 'Time In Market',
					'value': 26,
					'tooltip_display_name': 'Nothing defined in market'
				},
				{
					'display_name': 'Team',
					'value': 78
				},
				{
					'display_name': 'Usability',
					'value': 83,
					'tooltip_display_name': 'No tooltip contents defined'
				},
				{
					'display_name': 'Yield',
					'value': 43,
					'tooltip_display_name': 'No tooltip contents defined for yield'
				}
			]
		}
	},
	{
		'company_id': 25,
		'company_name': 'Mintos',
		'company_logo_url': 'https://testing4.azarus.es/img/logo/Mintos.png',
		'company_key_info': {
			'company_key_info_text': 'We provide retail investors with an easy ...',
			'company_name': 'Mintos',
			'company_number_of_investors': 43342,
			'company_start': '2013',
			'company_country_of_origin': 'Latvia',
			'company_total_invested_assets': {
				'amount': {
					'value': 2342443223,
					'currency_code': 'EUR'
				}
			}
		},
		'company_product_details': {
			'loan_types': 'All Loans',
			'minimum_investment': {
				'value': 10,
				'currency_code': 'EUR'
			},
			'autoinvest_option': true,
			'fees': false,
			'secondary_market': false,
			'supported_currencies': [
				{
					'currency_code': 'EUR'
				},
				{
					'currency_code': 'USD'
				},
				{
					'currency_code': 'GBP'
				},
				{
					'currency_code': 'RUB'
				}
			],
			'lending_return': 1,
			'buyback_guarantee': true,
			'collateral': false,
			'is_regulated': false,
			'crossborder': true
		},
		'company_extendedInfo': {
			'custodial_funds': 'Payasera',
			'transfer_funds': 'SEPA Bank Transfer',
			'withdrawals_fee': false,
			'secondary_market_fee': false,
			'sell_outs': false,
			'accepted_investors': 'Worldwide',
			'taxation': 'Country of Origin'
		},
		'company_commercial_offer_info': {
			'commercial_info_text': 'You will receive 5€ ....',
			'commercial_offer_url': 'www.mintos.com'
		},
		'company_winvestify_evaluation': {
			'global_evaluation_actual_value': 840,
			'global_evaluation_max': 1000,
			'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP',
			'details': [
				{
					'display_name': 'Transparency',
					'value': 24
				},
				{
					'display_name': 'Investor Protection',
					'value': 44,
					'tooltip_display_name': 'Nothing defined in protection'
				},
				{
					'display_name': 'Technology',
					'value': 62,
					'tooltip_display_name': 'Nothing defined yet in technology'
				},
				{
					'display_name': 'Customer Care',
					'value': 86
				},
				{
					'display_name': 'Risk Scoring',
					'value': 93,
					'tooltip_display_name': 'Nothing defined'
				},
				{
					'display_name': 'Regulated',
					'value': 33,
					'tooltip_display_name': 'Nothing defined in regulated'
				},
				{
					'display_name': 'Time In Market',
					'value': 26,
					'tooltip_display_name': 'Nothing defined in market'
				},
				{
					'display_name': 'Team',
					'value': 78
				},
				{
					'display_name': 'Usability',
					'value': 83,
					'tooltip_display_name': 'No tooltip contents defined'
				},
				{
					'display_name': 'Yield',
					'value': 43,
					'tooltip_display_name': 'No tooltip contents defined for yield'
				}
			]
		}
	}
]
