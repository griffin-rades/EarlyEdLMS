<?= $this->extend('Templates/Admin') ?>

<?= $this->section('content') ?>
	<ol class="breadcrumb">
		<li class="breadcrumb-item">
			<a href="<?= site_url('admin') ?>"><?= lang('Admin.homeBreadcrumbTitle') ?></a>
		</li>
		<li class="breadcrumb-item">
			<a href="<?= site_url('admin/groups') ?>"><?= lang('Admin.groupsBreadcrumbTitle') ?></a>
		</li>
		<li class="breadcrumb-item active"><?= lang('Admin.breadcrumbCommonEdit') ?></li>
	</ol>
	<?php if (session('errors')): ?>
		<div class="alert alert-danger">
			<?php foreach (session('errors') as $error) : ?>
				<?= esc($error) ?><br />
			<?php endforeach ?>
		</div>
	<?php elseif (session('infos')): ?>
		<div class="alert alert-info">
			<?php foreach (session('infos') as $info) : ?>
				<?= esc($info) ?><br />
			<?php endforeach ?>
		</div>
	<?php endif; ?>
	<div class="card mb-3">
		<div class="card-header">
			<?= lang('Admin.groupsEditHeader') ?>
		</div>
		<?= form_open('admin/groups/update/' . $group['id']) ?>
			<div class="card-body">
				<div class="form-group">
					<label><?= lang('Admin.groupsLabelNameCurrent') ?></label>
					<p><?= $group['name'] ?></p>
				</div>
				<div class="form-group">
					<label for="inputName"><?= lang('Admin.groupsLabelName') ?></label>
					<input type="text" class="form-control" name="name" id="inputName" placeholder="<?= lang('Admin.groupsLabelName') ?>">
				</div>
				<div class="form-group">
					<label><?= lang('Admin.groupsLabelDefinitionCurrent') ?></label>
					<p><?= $group['definition'] ?></p>
				</div>
				<div class="form-group">
					<label for="inputDefinition"><?= lang('Admin.groupsLabelDefinition') ?></label>
					<input type="text" class="form-control" name="definition" id="inputDefinition" placeholder="<?= lang('Admin.groupsLabelDefinition') ?>">
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="inputSubGroups"><?= lang('Admin.groupsLabelSubGroups') ?></label>
							<?php foreach ($groups as $subgroup): ?>
								<?php $subgroup['subgroup'] = (int) $subgroup['subgroup']; ?>
								<?php $subgroup['id'] = (int) $subgroup['id']; ?>
								<?php $group['id'] = (int) $group['id']; ?>
								<input type="hidden" name="sub_groups[<?= $subgroup['id'] ?>]" value="0">
								<div class="form-check">
									<label>
										<input type="checkbox" name="sub_groups[<?= $subgroup['id'] ?>]" value="1" <?= $subgroup['subgroup'] === 1 ? 'checked' : ($subgroup['id'] === 2 ? 'disabled' : ($subgroup['id'] === 3 ? 'disabled' : ($subgroup['id'] === $group['id'] ? 'disabled' : ''))) ?>>
										<?= $subgroup['definition'] ?> (<?= $subgroup['name'] ?>)
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="inputPerms"><?= lang('Admin.groupsLabelPerms') ?></label>
							<?php foreach ($perms as $perm): ?>
								<?php $perm['state'] = (int) $perm['state']; ?>
								<input type="hidden" name="perms[<?= $perm['id'] ?>]" value="-1">
								<div class="form-check">
									<label>
										<input type="checkbox" name="perms[<?= $perm['id'] ?>]" class="perm" <?= $perm['state'] !== -1 ? 'checked' : ''?>> <?= $perm['definition']?> (<?= $perm['name'] ?>)
									</label>
									<label>
										<input type="radio" name="perms[<?= $perm['id'] ?>]" value="1" <?= $perm['state'] === 1 ? 'checked' : ''?> <?= $perm['state'] === -1 ? 'disabled' : ''?>><?= lang('Admin.usersLabelAllow')?>
									</label>
									<label>
										<input type="radio" name="perms[<?= $perm['id'] ?>]" value="0" <?= $perm['state'] === 0 ? 'checked' : ''?> <?= $perm['state'] === -1 ? 'disabled' : ''?>><?= lang('Admin.usersLabelDeny')?>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button type="submit" class="btn btn-primary float-right"><?= lang('Admin.groupsEditSubmit') ?></button>
				<a href="<?= site_url('admin/groups') ?>" class="btn btn-warning"><?= lang('Admin.groupsLinkBack') ?></a>
			</div>
		<?= form_close() ?>
	</div>
<?= $this->endSection() ?>

<?= $this->section('footer') ?>
<script type="text/javascript">
  $(document).on('change', '.perm', function() {
	var name = $(this).attr('name');

	if ($(this).prop('checked')) {
	  $('input[type="radio"][name="'+ name + '"]').prop('checked', false).prop('disabled', false);
	  $('input[type="radio"][name="'+ name + '"]:eq(0)').prop('checked', true);
	} else {
	  $('input[type="radio"][name="'+ name + '"]').prop('checked', false).prop('disabled', true);
	}
  });
</script>
<?= $this->endSection() ?>
