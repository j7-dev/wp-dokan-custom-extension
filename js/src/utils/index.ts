import $ from 'jquery'

/**
 * 共同的 shipping row 的 id
 */

export const commonShippingRowId = 'common-shipping-row'

/**
 * 找出所有 vendor 的 shipping method 的交集
 *
 * @return string[] - 所有 vendor 的 shipping method 的交集
 */

export const getCommonShippingValues = (): string[] => {
  // 取得所有 vendor 的 shipping rows

  const shippingRows = $(
    '.woocommerce-checkout-review-order-table .woocommerce-shipping-totals',
  )

  const allShippingValueArrays: string[][] = []

  shippingRows.each((i, shippingRow) => {
    const vendorName = $(shippingRow).find('th').text() || 'unknown vendor'
    allShippingValueArrays[i] = []
    const shippingMethodNodes = $(shippingRow).find('ul#shipping_method > li')

    shippingMethodNodes.each((j, shippingMethodNode) => {
      const shippingMethodValue: string =
        $(shippingMethodNode).find('input').val() || ''
      allShippingValueArrays[i][j] = shippingMethodValue
    })
  })

  // 找出所有 vendor 的 shipping method 的交集

  const commonShippingValues = allShippingValueArrays.reduce((acc, cur) => {
    return acc.filter((e) => cur.includes(e))
  })

  return commonShippingValues
}
