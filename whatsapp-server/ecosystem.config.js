module.exports = {
  apps: [{
    name: 'whatsapp-gateway-absensi',
    script: 'server.js',
    env: {
      PORT: 3001,
      SESSION_NAME: 'absensi-wa-session'
    }
  }]
};
