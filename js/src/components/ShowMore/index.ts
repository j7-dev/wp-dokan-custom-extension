export const ShowMore = (): string => {
  const html = `
		<div class="text-right mt-4">
			<button type="button" class="button show-more-button">
				<span class="button-text">顯示每個供應商的運送方式</span>
				<span class="button-text" style="display:none;">隱藏每個供應商的運送方式</span>
			</button>
		</div>
	`

  return html
}
