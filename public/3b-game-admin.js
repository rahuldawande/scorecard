var admin = {
  ajax: function (opt) {
  // admin.ajax() : do AJAX call
  // PARAM opt : options

    // DATA
    var data = new FormData();
    for (var key in opt.data) {
      data.append(key, opt.data[key]);
    }

    // AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "3c-game-admin-ajax.php", true);
    xhr.onload = function () {
      if (typeof opt.load=="function") {
        opt.load(this.response);
      }
    };
    xhr.send(data);
  },

  list: function () {
  // admin.list() : show games

    admin.ajax({
      data : { req : "list" },
      load : function (res) {
        document.getElementById("container").innerHTML = res;
      }
    });
  },

  addEdit: function(id) {
  // admin.addEdit() : add/edit game
  // PARAM id : game id

    admin.ajax({
      data : {
        req : "addEdit",
        id : id ? id : ""
      },
      load : function (res) {
        document.getElementById("container").innerHTML = res;
      }
    });
  },

  save: function() {
  // admin.save() : save game

    var data = {
      id : document.getElementById("game_id").value,
      home : document.getElementById("game_home").value,
      away : document.getElementById("game_away").value
    };
    data.req = data.id=="" ? "add" : "edit" ;
    admin.ajax({
      data : data,
      load : function (res) {
        if (res=="OK") { admin.list(); }
        else { alert("Error saving game"); }
      }
    });
    return false;
  },

  del: function(id) {
  // admin.del() : delete game
  // PARAM id : game id

    if (confirm("Delete game?")) {
      admin.ajax({
        data : {
          req : "del",
          id : id
        },
        load : function (res) {
          if (res=="OK") { admin.list(); }
          else { alert("Error deleting game"); }
        }
      });
    }
  }
};

var score = {
  show : function(id) {
  // score.show() : show score for game
  // PARAM id : game id

    admin.ajax({
      data : {
        req : "score-show",
        id : id
      },
      load : function (res) {
        document.getElementById("container").innerHTML = res;
        score.history();
      }
    });
  },

  history : function() {
  // score.history() : show score history

    admin.ajax({
      data : {
        req : "score-history",
        id : document.getElementById("game_id").value
      },
      load : function (res) {
        document.getElementById("scores").innerHTML = res;
      }
    });
  },

  add : function() {
  // score.add() : add new score

    admin.ajax({
      data : {
        req : "score-add",
        id : document.getElementById("game_id").value,
        home : document.getElementById("score_home").value,
        away : document.getElementById("score_away").value,
        comment : document.getElementById("score_comment").value
      },
      load : function (res) {
        if (res=="OK") { 
          score.history();
          document.getElementById("score_comment").value = "";
        }
        else { alert("Error saving score"); }
      }
    });
    return false;
  },

  del : function(id, date) {
  // score.del() : delete score

    if (confirm("Delete score?")) {
      admin.ajax({
        data : {
          req : "score-del",
          id : id,
          date : date
        },
        load : function (res) {
          if (res=="OK") { score.history(); }
          else { alert("Error deleting score"); }
        }
      });
    }
  }
};

window.addEventListener("load", admin.list);