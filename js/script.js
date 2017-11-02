
let xhr = false;

let socketId = null;

$(document).ready(function() {

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "500",
        "timeOut": "3000",
        "extendedTimeOut": "100",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

});

const pusher = new Pusher($('meta[name="_key"]').attr('content'), {
    cluster: $('meta[name="_cluster"]').attr('content'),
    encrypted: true,
    authEndpoint: 'auth.php'
});

const channel = pusher.subscribe('presence-channel');

channel.bind('pusher:subscription_succeeded', function(members) {
    socketId = pusher.connection.socket_id;
    vm.members = members.count;
});

channel.bind('pusher:member_added', function(member) {
    // toastr["success"]("Player has joined");
    vm.members++;
});

channel.bind('pusher:member_removed', function(member) {
    // toastr["error"]("Player has left");
    vm.members--;
});

channel.bind('chat-event', function(data) {
    let squirl = vm.squirlz[data.id];
    messages.unshift({
        image: squirl.image,
        name: squirl.name,
        message: data.message,
        time: data.time,
        socketId: data.socketId
    });
});

channel.bind('select-event', function(data) {
    let items = data.acorns;
    $.each(items, function(k,v){
        acorns[k].active = parseInt(v.active);
    });
});

channel.bind('squirl-event', function(data) {
    if(vm.confirmSquirl === false) {
        if(data.socketId != socketId) {
            $.each(vm.squirlz, function(index, squirl) {
                if(squirl['locked'] == socketId) squirl['locked'] = false;
            });
            vm.squirlz[data.squirlId].locked = socketId;
        }
    }
});


let messages = [];

let acorns = [];

for(i = 1; i <= 25; i++) {
    let acorn = {
        number: i, 
        active: 1
    };
    acorns.push(acorn);
}

let vm = new Vue({

    el: '#wrapper',

    data: {

        messages: messages,
        acorns: acorns,
        members: '',
        mysquirl: '',
        confirmSquirl: false,

        squirlz: [{
            name: 'Ralph',
            image: 'images/characters/ralph.png',
            selected: false,
            locked: false
        },
        {
            name: 'Federico',
            image: 'images/characters/federico.png',
            selected: false,
            locked: false
        },
        {
            name: 'Bianca',
            image: 'images/characters/bianca.png',
            selected: false,
            locked: false
        },
        {
            name: 'Jay',
            image: 'images/characters/jay.png',
            selected: false,
            locked: false
        },
        {
            name: 'Tubz',
            image: 'images/characters/tubz.png',
           selected: false,
           locked: false
        }]

    }, // data


    methods: {

        selectSquirl: function(index, squirl) {
            if(this.confirmSquirl === false && squirl.locked === false) {
                squirl.id = index;
                this.mysquirl = squirl;
                $.ajax({
                    url: "pusher.php",
                    type: "POST",
                    data: {
                        type: 'squirl',
                        csrf: $('meta[name="_token"]').attr('content'),
                        socketId: socketId,
                        squirlId: index
                    }
                });
            }
        },

        getDelay: function(index) {
            return "animation-delay: " + ((index + 1) * 0.15) + "s";
        },

        getClass: function(index) {
            return index === 0 ? 'col-2 offset-1' : 'col-2';
        },

        lockSquirl: function() {
            if(this.confirmSquirl === false) {
                this.confirmSquirl = true;
                let name = this.mysquirl.name;
                $.each(this.squirlz, function(index, squirl) {
                    if(squirl['name'] != name) squirl['locked'] = true;
                });
            }
        }

    } // methods

}); // vue


function toggle(n){
    const key = n.number - 1;
    const items = acorns;
    items[key].active = n.active == 1 ? 0 : 1;
    $.ajax({
        url: "pusher.php",
        type: "POST",
        data: {
            type: 'select',
            csrf: $('meta[name="_token"]').attr('content'),

            data: items
        }
    });
} // toggle


function addMessage() {
    $("#messageForm").validate({
        rules: {
            name: {
                required: true
            },
            message: {
                required: true
            }
        },
        messages: {
            name: {
                required: "select a squirl"
            },
            message: {
                required: "message required"
            }
        },
        submitHandler: function(form) {
            if(xhr === false && socketId !== null) {
                const id = $('#id').val();
                $.ajax({
                    url: "pusher.php",
                    type: "POST",
                    data: {
                        type: 'chat',
                        socketId: socketId,
                        csrf: $('meta[name="_token"]').attr('content'),
                        id: $('#id').val(),
                        message: $('#message').val()
                    },
                    beforeSend: function() {
                        xhr = true;
                    },
                    success: function() {
                        $('#message').val("");
                    },
                    complete: function() {
                        xhr = false;
                    }
                });
            }
        }
    });
} // addMessage
