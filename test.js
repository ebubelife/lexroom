function liveRoom(roomUuid, token) {
    return {
        roomUuid: roomUuid,
        token: token,
        messageInput: '',
        messages: {!! json_encode($initialMessages) !!},
