<template>
  <div class="flex flex-col page  w-full px-24 justify-center items-center">
    <div
      class="flex flex-col privacy-text-container w-3/5 items-start text-base leading-loose pr-6"
    >
      <p class="text-2xl mt-10 mb-6">Privacy Policy</p>

      <p class>
        Protecting your private information is our priority. This Statement of Privacy applies to users of www.meetandmingle.net
        and Meet and Mingle Corporation and governs data collection and usage. For the purposes of this Privacy Policy, unless
        otherwise noted, all references to Meet and Mingle Corporation include Meet and Mingle. The Meet and Mingle website is
        a social networking site. By using the Meet and Mingle website, you consent to the data practices described in this state-
        ment.
      </p>

      <p class="font-bold pt-8">Collection of your Personal Information</p>

      <p class="py-8">
        Meet and Mingle may collect personally identifiable information, such as your: Name, E-mail Address, and Phone
        Number.
      </p>

      <p>
        Please keep in mind that if you directly disclose personally identifiable information or personally sensitive data through
        Meet and Mingle's public message boards, this information may be collected and used by others. Meet and Mingle
        encourages you to review the privacy statements of websites you choose to link to from Meet and Mingle so that you can
        understand how those websites collect, use and share your information. Meet and Mingle is not responsible for the priva-
        cy statements or other content on websites outside of the Meet and Mingle website. Use of your Personal Information
        Meet and Mingle collects and uses your personal information to operate its website(s) and deliver the services you have
        requested. Meet and Mingle may also use your personally identifiable information to inform you of other products or
        services available from Meet and Mingle and its affiliates. Meet and Mingle may also contact you via surveys to conduct
        research about your opinion of current services or of potential new services that may be offered. Meet and Mingle does
        not sell, rent or lease its customer lists to third parties. Meet and Mingle may, from time to time, contact you on behalf of
        external business partners about a particular offering that may be of interest to you. In those cases, your unique personal-
        ly identifiable information (e-mail, name, address, telephone number) is transferred to the third party. Meet and Mingle
        may share data with trusted partners to help perform statistical analysis, send you email or postal mail, provide customer
        support, or arrange for deliveries. All such third parties are prohibited from using your personal information except to
        provide these services to Meet and Mingle, and they are required to maintain the confidentiality of your information.
        Meet and Mingle may keep track of the websites and pages our users visit within Meet and Mingle, in order to determine
        what Meet and Mingle services are the most popular. This data is used to deliver customized content and advertising
        within Meet and Mingle to customers whose behavior indicates that they are interested in a particular subject area.
      </p>

      <p class="py-8">
        Meet and Mingle will disclose your personal information, without notice, only if required to do so by law or in the good
        faith belief that such action is necessary to: (a) conform to the edicts of the law or comply with legal process served on
        Meet and Mingle or the site; (b) protect and defend the rights or property of Meet and Mingle; and, (c) act under exigent
        circumstances to protect the personal safety of users of Meet and Mingle, or the public.
      </p>

      <p class="font-bold">Automatically Collected Information</p>

      <p class="py-8">
        Information about your computer hardware and software may be automatically collected by Meet and Mingle. This infor-
        mation can include: your IP address, browser type, domain names, access times and referring website addresses. This
        information is used for the operation of the service, to maintain quality of the service, and to provide general statistics
        regarding use of the Meet and Mingle website.
      </p>

      <p class="font-bold">Use of Cookies</p>

      <p class="py-8">
        The Meet and Mingle website may use "cookies" to help you personalize your online experience. A cookie is a text file that
        is placed on your hard disk by a web page server. Cookies cannot be used to run programs or deliver viruses to your
        computer. Cookies are uniquely assigned to you, and can only be read by a web server in the domain that issued the
        cookie to you.
      </p>

      <p>
        One of the primary purposes of cookies is to provide a convenience feature to save you time. The purpose of a cookie is
        to tell the Web server that you have returned to a specific page. For example, if you personalize Meet and Mingle pages,
        or register with Meet and Mingle site or services, a cookie helps Meet and Mingle to recall your specific information on subsequent visits. This simplifies the process of recording your personal information, such as billing addresses, shipping
        addresses, and so on. When you return to the same Meet and Mingle website, the information you previously provided
        can be retrieved, so you can easily use the Meet and Mingle features that you customized.
      </p>

      <p class="py-8">
        You have the ability to accept or decline cookies. Most Web browsers automatically accept cookies, but you can usually
        modify your browser setting to decline cookies if you prefer. If you choose to decline cookies, you may not be able to fully
        experience the interactive features of the Meet and Mingle services or websites you visit.
      </p>

      <p class="font-bold">Children Under Thirteen</p>

      <p class="py-8">
        Meet and Mingle does not knowingly collect personally identifiable information from children under the age of thirteen. If
        you are under the age of thirteen, you must ask your parent or guardian for permission to use this website.
      </p>

      <p class="font-bold">Disconnecting your Meet and Mingle Account from Third Party Websites</p>

      <p class="py-8">
        You will be able to connect your Meet and Mingle account to third party accounts. BY CONNECTING YOUR MEET AND
        MINGLE ACCOUNT TO YOUR THIRD PARTY ACCOUNT, YOU ACKNOWLEDGE AND AGREE THAT YOU ARE CONSENTING
        TO THE CONTINUOUS RELEASE OF INFORMATION ABOUT YOU TO OTHERS (IN ACCORDANCE WITH YOUR PRIVACY
        SETTINGS ON THOSE THIRD PARTY SITES). IF YOU DO NOT WANT INFORMATION ABOUT YOU, INCLUDING PERSONAL-
        LY IDENTIFYING INFORMATION, TO BE SHARED INTHIS MANNER, DO NOT USE THE THIS FEATURE. You may disconnect
        your account from a third party account at any time. Users may learn how to disconnect their accounts from third-party
        websites by contacting us via telephone.
      </p>

      <p class="font-bold">Opt-Out & Unsubscribe</p>

      <p class="py-8">
        We respect your privacy and give you an opportunity to opt-out of receiving announcements of certain information. Users
        may opt-out of receiving any or all communications from Meet and Mingle by contacting us here:
      </p>
    </div>

    <home-footer style="margin-top:auto;"></home-footer>
  </div>
</template>

<script>
export default {
  mounted() {},

  data() {
    return {};
  }
};
</script>

<style lang="scss" scoped>
.page {
  color: white;
  min-height: 90vh;
    background: url(/images/login_background.png);
  background-repeat: no-repeat;
  background-size: cover;
}

.privacy-text-container {
  height: 80vh;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  overflow-y: scroll;
}
</style>
