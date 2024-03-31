import $ from 'jquery'
import { ShowMore } from '@/components'
import { commonShippingRowId } from '@/utils'

/**
 * 複製第一個 ShippingRow
 * 然後只保留共同的 shipping method
 */

export const renderCommonShippingRow = (commonShippingValues: string[]) => {
  const firstShippingRow = $(
    '.woocommerce-checkout-review-order-table .woocommerce-shipping-totals',
  ).first()

  if (commonShippingValues.length) {
    // 複製第一個 shipping row

    const shippingRow = firstShippingRow.clone()

    // 移除 class 才不會初始就被隱藏

    shippingRow.removeClass('woocommerce-shipping-totals shipping')

    // 移除不必要的說明

    shippingRow.find('.woocommerce-shipping-contents').remove()

    // 移除每個 input 的 id & name attribute
    // 並加上指定的 name

    const inputName = '__shipping_method'
    shippingRow.find('input').each((i, inputNode) => {
      const value = $(inputNode).val() || ''

      $(inputNode).removeAttr('id name class data-index')
      $(inputNode)
        .attr('name', inputName)
        .attr('data-value', value)
        .attr('id', `${inputName}_${value}`)
      $(inputNode)
        .next('label')
        .attr('data-value', value)
        .attr('for', `${inputName}_${value}`)
    })

    // 加上指定的 id & name

    shippingRow.attr('id', commonShippingRowId)
    shippingRow.attr('name', 'common-shipping-method')

    // 插入 ShowMore button

    shippingRow.find('td').append(ShowMore()).removeAttr('data-title')

    // 修改文字

    shippingRow.find('th').text('供應商共通運送方式')
    shippingRow.find('td').data('title', '供應商共通運送方式')

    // 移除不是共同的 shipping method

    shippingRow
      .find('ul#shipping_method > li')
      .each((i, shippingMethodNode) => {
        const shippingMethodValue: string =
          $(shippingMethodNode).find('input').val() || ''
        if (!commonShippingValues.includes(shippingMethodValue)) {
          $(shippingMethodNode).remove()
        }
      })

    // 將共同的 shipping row 插入到第一個 shipping row 之前

    firstShippingRow.before(shippingRow)
  } else {
    firstShippingRow.before(`
		<tr>
			<td colspan="2">
				<p>找不到共通的運送方法</p>
				${ShowMore()}
			</td>
		</tr>
		`)
  }
}

/**
 * 事件監聽
 */

export const addEventListener = () => {
  handleShowMoreButton()

  handleSyncSelectedShippingMethod()
}

/**
 * 顯示更多按鈕
 */

const handleShowMoreButton = () => {
  $('.show-more-button').on('click', function () {
    $('.woocommerce-shipping-totals').toggle()
    $(this).find('.button-text').toggle()
  })
}

/**
 * 同步運費選項
 */

const handleSyncSelectedShippingMethod = () => {
  const theCloneRow = $(`#${commonShippingRowId}`)

  // theCloneRow.find('label').on('click', function (e) {
  //   e.stopPropagation()
  //   e.preventDefault()
  // })

  theCloneRow.find('input, label').on('click', function (e) {
    e.stopPropagation()
    e.preventDefault()

    const value = $(this).data('value')

    $(
      '.woocommerce-checkout-review-order-table .woocommerce-shipping-totals',
    ).each((i, shippingRow) => {
      $(shippingRow).find(`input[value="${value}"]`).prop('checked', true)
      $(`input[name="shipping_method[${i}]"]`).val(value)
    })

    $(this).prev('input').prop('checked', true)

    // get form data of <form name="checkout">

    const checkoutForm = $('form[name="checkout"]')
    const formData = checkoutForm.serializeArray()

    // change formdata value to value to all fields with name starting with "shipping_method"

    formData.forEach((field) => {
      if (field.name.startsWith('shipping_method')) {
        field.value = value || ''
      }
    })

    // 這邊不能使用 trigger('update_checkout')，刷新頁面時，原本選中的運送方式與後端選中的運送方式不同步
    // 不知道什麼原因，但使用原生點擊事件就不會有這問題
    // 猜測是 點擊事件做了額外的事情，而 trigger('update_checkout') 沒有
    // $('body').trigger('update_checkout')

    $(
      '.woocommerce-checkout-review-order-table .woocommerce-shipping-totals',
    ).each((i, shippingRow) => {
      $(shippingRow).find(`input[value="${value}"]`).trigger('click')
    })
  })
}
