$(document).ready(function(){
	AdminManager.Init();
});

let AdminManager = {
	admins: null,
	selectedAdmin: null,
	addAdminModal: null,
	editAdminModal: null,
	reinitAdminPasswordModal: null,
	removeAdminModal: null,

	Init: function () {
		this.addAdminModal = new bootstrap.Modal('#addAdminModal');
		this.editAdminModal = new bootstrap.Modal('#editAdminModal');
		this.reinitAdminPasswordModal = new bootstrap.Modal('#reinitAdminPasswordModal');
		this.removeAdminModal = new bootstrap.Modal('#removeAdminModal');

		$("#addAdminButton").click(function(){
			AdminManager.selectedAdmin = null;
			$('#addAdminModal').find("input, textarea, select").val("");
			AdminManager.addAdminModal.show();
		});
		
		$('#addAdminModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "admin_add",
				type: "POST",
				data: {
					email: $('#addAdminModal').find("#emailInput").val(),
					name: $('#addAdminModal').find("#nameInput").val(),
					roles: $('#addAdminModal').find("#rolesInput").val()
				},
				success: function() {
					AdminManager.Refresh();
					AdminManager.addAdminModal.hide();
				}
			});
	
			return false;
		});
	
		$('#editAdminModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "admin_edit",
				type: "POST",
				data: {
					id_admin: AdminManager.selectedAdmin.id_admin,
					email: $('#editAdminModal').find("#emailInput").val(),
					name: $('#editAdminModal').find("#nameInput").val(),
					roles: $('#editAdminModal').find("#rolesInput").val()
				},
				success: function() {
					AdminManager.Refresh();
					AdminManager.editAdminModal.hide();
				}
			});
	
			return false;
		});

		$('#reinitAdminPasswordModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "reinit_admin_password",
				type: "POST",
				data: {
					id_admin: AdminManager.selectedAdmin.id_admin
				},
				success: function() {
					AdminManager.Refresh();
					AdminManager.reinitAdminPasswordModal.hide();
				}
			});
	
			return false;
		});

		$('#removeAdminModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "admin_remove",
				type: "POST",
				data: {
					id_admin: AdminManager.selectedAdmin.id_admin
				},
				success: function() {
					AdminManager.Refresh();
					AdminManager.removeAdminModal.hide();
				}
			});
	
			return false;
		});
	
		this.Refresh();
	},
	
	Refresh: function () {
		$.ajax({
			url: api_url + "admins_list",
			type: "POST",
			success: function(data) {
				AdminManager.admins = data.admins;
				
				$("#nbAdmins").html(AdminManager.admins.length + "");
				$("#tableAdmins tbody").html("");
				
				for (let i = 0; i < AdminManager.admins.length; i ++) {
					AdminManager.AddAdmin(AdminManager.admins[i]);
				}
			}
		});
	},
	
	AddAdmin: function(admin) {
		let actions = $("<td class='text-end'><div class='btn-group'></div></td>");
		actions.find("div").append("<button class='modify-admin btn btn-secondary' data-id='" + admin.id_admin + "'><i class='fas fa-pen'></i></button> ");
		actions.find("div").append("<button class='reinit-admin-password btn btn-info' data-id='" + admin.id_admin + "'><i class='fas fa-sync-alt'></i></button> ");
		actions.find("div").append("<button class='remove-admin btn btn-danger' data-id='" + admin.id_admin + "'><i class='fas fa-trash-alt'></i></button>");
		
		actions.find(".modify-admin").click(function(){
			AdminManager.selectedAdmin = admin;
			
			$('#editAdminModal').find("#nameInput").val(AdminManager.selectedAdmin.name);
			$('#editAdminModal').find("#emailInput").val(AdminManager.selectedAdmin.email);
			$('#editAdminModal').find("#rolesInput").val(AdminManager.selectedAdmin.roles);
			
			AdminManager.editAdminModal.show();
		});

		actions.find(".reinit-admin-password").click(function(){
			AdminManager.selectedAdmin = admin;
			$("#reinitAdminPasswordModal").find("#nameAdmin").html(AdminManager.selectedAdmin.name);
			AdminManager.reinitAdminPasswordModal.show();
		});
		
		actions.find(".remove-admin").click(function(){
			AdminManager.selectedAdmin = admin;
			$("#removeAdminModal").find("#nameAdmin").html(AdminManager.selectedAdmin.name);
			AdminManager.removeAdminModal.show();
		});
	
		var row = $("<tr></tr>");
		row.append("<td>" + admin.name + "</td>");
		row.append("<td>" + admin.email + "</td>");
		row.append("<td>" + admin.roles + "</td>");
		row.append(actions);
		
		$("#tableAdmins tbody").append(row);
	},
	
	GetAdmin: function(id_admin) {
		for (let i = 0; i < AdminManager.admins.length; i ++) {
			if (AdminManager.admins[i].id_admin === id_admin) {
				return AdminManager.admins[i];
			}
		}
	}
}