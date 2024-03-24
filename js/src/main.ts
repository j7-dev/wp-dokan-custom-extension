import $ from 'jquery'
import { getCommonShippingValues } from '@/utils'
import { renderCommonShippingRow, addEventListener } from '@/render'
import './style.scss'

function main() {
  const shippingRows = $(
    '.woocommerce-checkout-review-order-table .woocommerce-shipping-totals',
  )

  /**
   * 如果購物車物品只有一個 vendor，就不用跑
   */

  if (shippingRows.length <= 1) {
    shippingRows.show()
    return
  }

  const commonShippingValues = getCommonShippingValues()

  // render

  renderCommonShippingRow(commonShippingValues)

  addEventListener()
}

$(document.body).on('updated_checkout', function () {
  main()
})
