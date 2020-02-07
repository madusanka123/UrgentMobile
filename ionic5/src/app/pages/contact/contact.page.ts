import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute  } from '@angular/router';
import { ToastController, LoadingController, AlertController, NavController } from '@ionic/angular';
import { AccessProviders } from '../../providers/access-providers';
import { Storage } from '@ionic/storage';

// import { Contacts, Contacat, ContactName, ContactField } from '@ionic-native/contacts/ngx';
// import { CallNumber } from '@ionic-native/call-number/ngx';
// import { SMS } from '@ionic-native/sms/ngx';

@Component({
  selector: 'app-contact',
  templateUrl: './contact.page.html',
  styleUrls: ['./contact.page.scss'],
})
export class ContactPage implements OnInit {
  //myContacts: Contact[] = [];

  datastorage: any;
  name:string;

  users: any=[];
  limit: number = 13; //limit get data
  start: number = 0;

  my_email:string;

  constructor(
    private router: Router,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController,
    private alertCtrl: AlertController,
    private accsPrvds: AccessProviders,
    private storage: Storage,
    // private contacts: Contacts,
    // private callNumber: CallNumber,
    // private sms: SMS,
    public navCtrl: NavController
  ) { 
    
    
  }

  // loadContacts(){
  //   let options = {
  //     filter: '',
  //     multiple: true,
  //     hasPhoneNumber: true
  //   };

  //   this.contacts.find(['*'], options).then((contacts: Contact[]) =>{
  //     this.myContacts = contacts;
  //     console.log('Contacts:', contacts);
  //   });
  // }

  // sendSms(contact: Contact){
  //   this.sms.send(contact.phoneNumber[ 0 ].value, 'I am in an emergensy, please call me..');
  // }

  // call(contact: Contact){
  //   this.callNumber.callNumber(contact.phoneNumber[ 0 ].value, true);
  // }


  ngOnInit() {
    this.storage.get('email_session').then((res)=>{
      this.my_email=res;
      this.presentToast(res);
    });
  }

  ionViewDidEnter(){
    this.storage.get('storage-xxx').then((res)=>{
      console.log(res);
      this.datastorage = res;
      this.name = this.datastorage.Name;

    });

    this.start = 0;
    this.users = [];
    this.loadUsers();
  }

  async doRefresh(event){
    const loader = await this.loadingCtrl.create({ 
      message: 'Please wait......'
    });
    loader.present();

    this.ionViewDidEnter();
    event.target.complete();

    loader.dismiss();
  }

  loadData(event:any){
    this.start += this.limit;
    setTimeout(() =>{
      this.loadUsers().then(()=>{
        event.target.complete();
      });
    }, 500);

  }

  async loadUsers(){
    return new Promise(resolve => {
        let body = {
          aksi: 'load_users',
          start: this.start,
          limit: this.limit,
          my_email:this.my_email
        }

        this.accsPrvds.postData(body, 'proses_api.php').subscribe((res:any)=>{
         for(let datas of res.result){ //specail if you want to use infiniti scroll load data per limit
           this.users.push(datas);
         }
         resolve(true);
        });
      });
  }    

  async delData(a){
    return new Promise(resolve => {
      let body = {
        aksi: 'del_users',
        id: a
      }

      this.accsPrvds.postData(body, 'proses_api.php').subscribe((res:any)=>{
        if(res.success==true){
          this.presentToast('Delete successfuly');
          this.ionViewDidEnter();
        }else{
          this.presentToast('Delete error');
        }
      });
    });
  }

  async presentToast(a){
    const toast = await this.toastCtrl.create({
      message: a,
      duration: 1500
    });
    toast.present();
  }


  // async prosesLogout(){
  //   this.storage.clear();
  //   this.navCtrl.navigateRoot(['/intro']);
  //   const toast = await this.toastCtrl.create({
  //     message: 'Logout successfuly',
  //     duration: 1500
  //   });
  //   toast.present();
  // }

  openCrud(a){
    this.router.navigate(['/crud/'+a]);
  }
}
 