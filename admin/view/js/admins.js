$(document).ready(function(){
	AdminManager.Init();
});

var AdminManager = {
	admins: null,
	selectedAdmin : null,

	Init: function () {		
		$("#addAdminButton").click(function(){
			AdminManager.selectedAdmin = null;
			$('#addAdminModal').find("input, textarea, select").val("");
			$('#addAdminModal').modal();
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
					$('#addAdminModal').modal('hide');
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
					$('#editAdminModal').modal('hide');
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
					$('#reinitAdminPasswordModal').modal('hide');
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
					$('#removeAdminModal').modal('hide');
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
				
				for(var i = 0; i < AdminManager.admins.length; i ++)
					AdminManager.AddAdmin(AdminManager.admins[i]);
			}
		});
	},
	
	AddAdmin: function(admin) {
		var actions = $("<td class='text-right'><div class='btn-group'></div></td>");
		actions.find("div").append("<button class='modify-admin btn btn-secondary' data-id='" + admin.id_admin + "'><i class='fas fa-pen'></i></button> ");
		actions.find("div").append("<button class='reinit-admin-password btn btn-info' data-id='" + admin.id_admin + "'><i class='fas fa-sync-alt'></i></button> ");
		actions.find("div").append("<button class='remove-admin btn btn-danger' data-id='" + admin.id_admin + "'><i class='fas fa-trash-alt'></i></button>");
		
		actions.find(".modify-admin").click(function(){
			AdminManager.selectedAdmin = admin;
			
			$('#editAdminModal').find("#nameInput").val(AdminManager.selectedAdmin.name);
			$('#editAdminModal').find("#emailInput").val(AdminManager.selectedAdmin.email);
			$('#editAdminModal').find("#rolesInput").val(AdminManager.selectedAdmin.roles);
			
			$('#editAdminModal').modal();
		});

		actions.find(".reinit-admin-password").click(function(){
			AdminManager.selectedAdmin = admin;
			$("#reinitAdminPasswordModal").find("#nameAdmin").html(AdminManager.selectedAdmin.name);
			$('#reinitAdminPasswordModal').modal();
		});
		
		actions.find(".remove-admin").click(function(){
			AdminManager.selectedAdmin = admin;
			$("#removeAdminModal").find("#nameAdmin").html(AdminManager.selectedAdmin.name);
			$('#removeAdminModal').modal();
		});
	
		var row = $("<tr></tr>");
		row.append("<td>" + admin.name + "</td>");
		row.append("<td>" + admin.email + "</td>");
		row.append("<td>" + admin.roles + "</td>");
		row.append(actions);
		
		$("#tableAdmins tbody").append(row);
	},
	
	GetAdmin: function(id_admin) {
		for(var i = 0; i < AdminManager.admins.length; i ++)
		{
			if(AdminManager.admins[i].id_admin == id_admin)
				return AdminManager.admins[i];
		}
	}
}