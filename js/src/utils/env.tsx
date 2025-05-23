/* eslint-disable @typescript-eslint/ban-ts-comment */
// @ts-nocheck

const APP_DOMAIN = 'wp_ai_bazi_data' as string
export const snake = window?.[APP_DOMAIN]?.env?.SNAKE || 'my_app'
export const appName = window?.[APP_DOMAIN]?.env?.APP_NAME || 'My App'
export const kebab = window?.[APP_DOMAIN]?.env?.KEBAB || 'my-app'
export const app1Selector = window?.[APP_DOMAIN]?.env?.APP1_SELECTOR || 'bazi-page'
export const app2Selector =
  window?.[APP_DOMAIN]?.env?.APP2_SELECTOR || 'my_app_metabox'
export const apiUrl = window?.wpApiSettings?.root || '/wp-json'
export const ajaxUrl =
  window?.[APP_DOMAIN]?.env?.ajaxUrl || '/wp-admin/admin-ajax.php'
export const siteUrl = window?.[APP_DOMAIN]?.env?.siteUrl || '/'
export const currentUserId = window?.[APP_DOMAIN]?.env?.userId || '0'
export const postId = window?.[APP_DOMAIN]?.env?.postId || '0'
export const permalink = window?.[APP_DOMAIN]?.env?.permalink || '/'
export const apiTimeout = '30000'
export const ajaxNonce = window?.[APP_DOMAIN]?.env?.nonce || ''
