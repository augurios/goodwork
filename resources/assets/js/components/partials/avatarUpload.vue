<template>
<div>
  <!-- <form method="post" enctype="multipart/form-data"> -->
    <div v-show="!croppieImage">
      <input type="file" name="avatar" id="avatar" accept="image/*" @change="selectFile" class="hidden">
    
      <button onclick="document.getElementById('avatar').click(); return false;" 
        class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold p-4 py-2 rounded shadow hover:shadow-lg">
        {{ 'Change Your Avatar' | localize }}
      </button>
    </div>
    <div v-show="croppieImage">
       <vue-croppie ref="croppieRef" :enableOrientation="true" :boundary="{ width: 200, height: 200}" :viewport="{ width:128, height:128, 'type':'square' }">
      </vue-croppie>
      <div class="text-center">
        <button @click="uploadImage" 
        class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold p-4 py-2 rounded shadow hover:shadow-lg">
        {{ 'Salvar' | localize }}
        </button>
      </div>
    </div>

    
  <!-- </form> -->
</div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  data: () => ({
    user: user,
    croppieImage: null,
    cropped: null,
  }),

  methods: {
    ...mapActions([
      'showNotification',
    ]),
    selectFile (e) {
      let files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;

      let reader = new FileReader()
      
      let thisSub = this;
      reader.onload = e => {
        thisSub.croppieImage = e.target.result;
        thisSub.$refs.croppieRef.bind({
          url: e.target.result
        });
        // this.$emit('image-loaded', e.target.result)
      }
      reader.readAsDataURL(files[0])
    },
    uploadImage () {

      let options = {
        type: 'base64',
        size: { width: 512, height: 512 },
        format: 'jpeg'
      };
      
      this.$refs.croppieRef.result(options, output => {
        this.cropped = output;
        this.postImage();
      });
      
    },
    baseToFile(base){
      var arr = base.split(','),
            mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), 
            n = bstr.length, 
            u8arr = new Uint8Array(n);
            
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
      var newFile = new File([u8arr], this.user.username, {lastModified: new Date()})
      return newFile;
    },
    postImage() {
      this.$emit('image-loaded', this.cropped)

      let data = new FormData()

      data.append('avatar', this.baseToFile(this.cropped));

      axios.post('/users/' + this.user.username + '/avatar', data)
        .then((response) => {
          this.showNotification({type: response.data.status, message: response.data.message})
          this.user.avatar = response.data.avatar
          this.croppieImage = null;
        })
        .catch((error) => {
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
        })
    }
  }
}
</script>
