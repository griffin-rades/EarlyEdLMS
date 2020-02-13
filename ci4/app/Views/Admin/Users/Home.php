<?= $this->extend('Templates/Admin') ?>

<?= $this->section('content') ?>
	<ol class="breadcrumb">
		<li class="breadcrumb-item">
			<a href="<?= site_url('admin') ?>"><?= lang('Admin.homeBreadcrumbTitle') ?></a>
		</li>
		<li class="breadcrumb-item active"><?= lang('Admin.usersBreadcrumbTitle') ?></li>
	</ol>
	<?php if (session('infos')): ?>
		<div class="alert alert-info">
			<?php foreach (session('infos') as $info) : ?>
				<?= esc($info) ?><br />
			<?php endforeach ?>
		</div>
	<?php endif; ?>
	<div class="card mb-3">
		<div class="card-header">
			<a href="<?= site_url('admin/users/new') ?>" class="btn btn-sm btn-success float-right"><?= lang('Admin.usersLinkNew') ?></a>
			<div class="pt-1"><?= lang('Admin.usersIndexHeader') ?></div>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table mb-0">
					<thead>
						<tr>
							<th scope="col" class="text-center"><?= lang('Admin.usersLabelId') ?></th>
							<th scope="col"><?= lang('Admin.usersLabelEmail') ?></th>
							<th scope="col"><?= lang('Admin.usersLabelUsername') ?></th>
							<th scope="col" class="text-center"><?= lang('Admin.usersLabelGroups') ?></th>
							<th scope="col" class="text-center"><?= lang('Admin.usersLabelPerms') ?></th>
							<th scope="col" class="text-center"><?= lang('Admin.usersLabelBanned') ?></th>
							<th scope="col"><?= lang('Admin.usersLabelCreatedAt') ?></th>
							<th scope="col"><?= lang('Admin.usersLabelUpdatedAt') ?></th>
							<th scope="col"><?= lang('Admin.usersLabelLastIPAddress') ?></th>
							<th scope="col"><?= lang('Admin.usersLabelLastActivity') ?></th>
							<th scope="col"><?= lang('Admin.usersLabelLastLogin') ?></th>
							<th scope="col"></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($users as $user): ?>
							<tr>
								<th scope="row" class="text-center"><?= $user['id'] ?></th>
								<td><?= $user['email'] ?></td>
								<td><?= $user['username'] ?></td>
								<td class="text-center"><?= count(get_user_groups($user['id'])) ?></td>
								<td class="text-center"><?= count(get_user_perms($user['id'], 1)) ?></td>
								<td class="text-center"><?= $user['banned'] === 1 ? 'Yes' : 'No' ?></td>
								<td><?= $user['created_at'] ?></td>
								<td><?= $user['updated_at'] ?></td>
								<td><?= $user['last_ip_address'] ?></td>
								<td><?= $user['last_activity'] ?></td>
								<td><?= $user['last_login'] ?></td>
								<td class="text-right p-1">
									<div class="btn-group">
										<a href="<?= site_url('admin/users/show/' . $user['id']) ?>" class="btn btn-info"><i class="fa fa-fw fa-info-circle"></i></a>
										<a href="<?= site_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-warning"><i class="fa fa-fw fa-pencil-alt"></i></a>
										<a href="<?= site_url('admin/users/delete/' . $user['id']) ?>" class="btn btn-danger"><i class="fa fa-fw fa-times"></i></a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="card-footer">
			<?= $pagerLinks ?>
		</div>
	</div>
<?= $this->endSection() ?>
