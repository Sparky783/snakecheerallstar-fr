$(document).ready(function(){
	UserManager.Init();
});

var UserManager = {
	users: null,
	selectedUser : null,

	Init: function () {		
		$("#addUserButton").click(function(){
			UserManager.selectedUser = null;
			$('#addUserModal').find("input, textarea, select").val("");
			$('#addUserModal').modal();
		});
		
		$('#addUserModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "user_add",
				type: "POST",
				data: {
					email: $('#addUserModal').find("#emailInput").val(),
					name: $('#addUserModal').find("#nameInput").val(),
					status: $('#addUserModal').find("#rightInput").val()
				},
				success: function() {
					UserManager.Refresh();
					$('#addUserModal').modal('hide');
				}
			});
	
			return false;
		});
	
		$('#editUserModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "user_edit",
				type: "POST",
				data: {
					id_user: UserManager.selectedUser.id_user,
					email: $('#editUserModal').find("#emailInput").val(),
					name: $('#editUserModal').find("#nameInput").val(),
					status: $('#editUserModal').find("#rightInput").val()
				},
				success: function() {
					UserManager.Refresh();
					$('#editUserModal').modal('hide');
				}
			});
	
			return false;
		});

		$('#reinitUserPasswordModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "reinit_user_password",
				type: "POST",
				data: {
					id_user: UserManager.selectedUser.id_user
				},
				success: function() {
					UserManager.Refresh();
					$('#reinitUserPasswordModal').modal('hide');
				}
			});
	
			return false;
		});

		$('#removeUserModal').find("form").submit(function(){
			$.ajax({
				url: api_url + "user_remove",
				type: "POST",
				data: {
					id_user: UserManager.selectedUser.id_user
				},
				success: function() {
					UserManager.Refresh();
					$('#removeUserModal').modal('hide');
				}
			});
	
			return false;
		});
	
		this.Refresh();
	},
	
	Refresh: function () {
		$.ajax({
			url: api_url + "users_list",
			type: "POST",
			success: function(data) {
				UserManager.users = data.users;
				
				$("#nbUsers").html(UserManager.users.length + "");
				
				$("#tableUsers tbody").html("");
				
				for(var i = 0; i < UserManager.users.length; i ++)
					UserManager.AddUser(UserManager.users[i]);
			}
		});
	},
	
	AddUser: function(user) {
		var actions = $("<td class='text-right'><div class='btn-group'></div></td>");
		actions.find("div").append("<button class='modify-user btn btn-secondary' data-id='" + user.id_user + "'><i class='fas fa-pen'></i></button> ");
		actions.find("div").append("<button class='reinit-user-password btn btn-info' data-id='" + user.id_user + "'><i class='fas fa-sync-alt'></i></button> ");
		actions.find("div").append("<button class='remove-user btn btn-danger' data-id='" + user.id_user + "'><i class='fas fa-trash-alt'></i></button>");
		
		actions.find(".modify-user").click(function(){
			UserManager.selectedUser = user;
			
			$('#editUserModal').find("#nameInput").val(UserManager.selectedUser.name);
			$('#editUserModal').find("#emailInput").val(UserManager.selectedUser.email);
			$('#editUserModal').find("#rightInput").val(UserManager.selectedUser.status);
			
			$('#editUserModal').modal();
		});

		actions.find(".reinit-user-password").click(function(){
			UserManager.selectedUser = user;
			$("#reinitUserPasswordModal").find("#nameUser").html(UserManager.selectedUser.name);
			$('#reinitUserPasswordModal').modal();
		});
		
		actions.find(".remove-user").click(function(){
			UserManager.selectedUser = user;
			$("#removeUserModal").find("#nameUser").html(UserManager.selectedUser.name);
			$('#removeUserModal').modal();
		});
	
		var row = $("<tr></tr>");
		row.append("<td>" + user.name + "</td>");
		row.append("<td>" + user.email + "</td>");
		row.append("<td>" + user.status + "</td>");
		row.append(actions);
		
		$("#tableUsers tbody").append(row);
	},
	
	GetUser: function(id_user) {
		for(var i = 0; i < UserManager.users.length; i ++)
		{
			if(UserManager.users[i].id_user == id_user)
				return UserManager.users[i];
		}
	}
}