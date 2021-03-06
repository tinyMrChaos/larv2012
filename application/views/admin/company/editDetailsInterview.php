<h1>Personliga Samtal <?=$company['org_name']; ?></h1>
<?=Form::open('/admin/company/detailsInterview/'.$company['company_id'].'/edit/'); ?>
<table>
	<thead>
		<th colspan="2">
		    <?=Form::submit('submit', 'Uppdatera');?>
		    <a href="javascript:updatePDF(<?php echo $company['company_id']; ?>)">Update Company PDF</a>
		</th>
		<tr>
			<th style="width: 150px;">Fält</th>
			<th style="width: 150px;">Data</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Erbjuder Personligt Samtal?</td>
			<td><?=Form::checkbox('interview_offer', 'Ja, Yes', ($company['interview_offer'] == 'Ja, Yes' ?true:false));?></td>
		</tr>
		<tr>
			<td>Information om Samtalet</td>
			<td><?=Form::textarea('interview_info', $company['interview_info']); ?></td>
		</tr>
		<tr>
			<td>Samma kontakt som profil?</td>
			<td><?=Form::checkbox('interview_contact_same', 'Ja, Yes', ($company['interview_contact_same'] == 'Ja, Yes' ?true:false));?></td>
		</tr>
		<tr>
			<td>Kontakt Förnamn</td>
			<td><?=Form::input('interview_contact_firstname', $company['interview_contact_firstname']); ?></td>
		</tr>
		<tr>
			<td>Kontakt Efternamn</td>
			<td><?=Form::input('interview_contact_lastname', $company['interview_contact_lastname']); ?></td>
		</tr>
		<tr>
			<td>Kontakt Epost</td>
			<td><?=Form::input('interview_contact_email', $company['interview_contact_email']); ?></td>
		</tr>
		<tr>
			<td>Kontakt Mobil</td>
			<td><?=Form::input('interview_contact_cell', $company['interview_contact_cell']); ?></td>
		</tr>
	</tbody>
</table>
<?=Form::submit('submit', 'Uppdatera');?>
<?=Form::close(); ?>
