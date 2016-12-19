<?php
	$presenter = new Illuminate\Pagination\BootstrapPresenter($paginator);
?>

<?php if ($paginator->getLastPage() > 1): ?>
	<div class="paginator">
		<ul class="pagination">
			<?php echo $presenter->render(); ?>
		</ul>
	</div>
<?php endif; ?>