"use strict";

(window.webpackJsonp = window.webpackJsonp || []).push([[112, 29, 71, 75, 98, 101, 102, 103, 105], {
  1140: function _(t, e, n) {
    t.exports = n.p + "img/positive-vote.ca372aa.svg";
  },
  1141: function _(t, e, n) {
    "use strict";

    n(865);
  },
  1142: function _(t, e, n) {
    var o = n(36)(!1);
    o.push([t.i, '\n.st0[data-v-320c7c50] {\n  fill: url(#path2995-1-0_1_);\n}\n.st1[data-v-320c7c50] {\n  fill: #c8daea;\n}\n.st2[data-v-320c7c50] {\n  fill: #a9c9dd;\n}\n.st3[data-v-320c7c50] {\n  fill: url(#path2991_1_);\n}\n.vote-icons[data-v-320c7c50] {\n  margin-bottom: 65px;\n}\n.mb-30[data-v-320c7c50] {\n  margin-bottom: 30px;\n}\n.sharing[data-v-320c7c50] {\n  display: flex;\n}\n.share-network-list[data-v-320c7c50] {\n  display: flex;\n  flex-direction: row;\n  flex-wrap: wrap;\n  justify-content: center;\n  max-width: 1000px;\n  margin: auto;\n}\na[class^="share-network-"][data-v-320c7c50] {\n  flex: none;\n  color: #ffffff;\n  background-color: #333;\n  border-radius: 3px;\n  width: 30px;\n  height: 30px;\n  overflow: hidden;\n  display: flex;\n  justify-content: center;\n  align-items: center;\n  cursor: pointer;\n  margin: 0 10px 10px 0;\n}\na[class^="share-network-"] .fah[data-v-320c7c50] {\n  background-color: rgba(0, 0, 0, 0.2);\n  padding: 10px;\n  flex: 0 1 auto;\n}\na[class^="share-network-"] span[data-v-320c7c50] {\n  padding: 0 10px;\n  flex: 1 1 0%;\n  font-weight: 500;\n}\n.smsVote[data-v-320c7c50] {\n  padding-top: 15px;\n}\n.initiatives-side__vote__voice[data-v-320c7c50] {\n  position: relative;\n  opacity: 1;\n  right: 15px;\n}\n.initiatives-side__vote__voice a[data-v-320c7c50] {\n  position: relative;\n  z-index: 1;\n  display: flex;\n  flex-direction: column;\n  align-items: center;\n  justify-content: center;\n  width: 55px;\n  text-align: center;\n  line-height: 16px;\n}\n.initiatives-side__vote__voice a > span[data-v-320c7c50] {\n  margin-top: 12px;\n  font-weight: 500;\n  line-height: 17px;\n  font-size: 16px;\n  color: #007791;\n}\n.voteanim[data-v-320c7c50] {\n  position: absolute;\n  height: 100px;\n  top: -80px;\n  right: -32px;\n  transform: rotateZ(25deg);\n  z-index: 0;\n  filter: grayscale(100%);\n}\n.initiatives-side__vote__voice.active[data-v-320c7c50],\n.initiatives-side__vote__voice[data-v-320c7c50]:hover {\n  opacity: 1;\n}\n.initiatives-side__vote__voice img.image[data-v-320c7c50],\n.initiatives-side__vote__voice svg.image[data-v-320c7c50] {\n  animation: leaves-320c7c50 1.5s ease-in-out infinite alternate;\n  -webkit-animation: leaves-320c7c50 1.5s ease-in-out infinite alternate;\n}\n.initiatives-side__vote__voice.active img.image[data-v-320c7c50],\n.initiatives-side__vote__voice img.image[data-v-320c7c50] {\n  transform: rotateZ(30deg);\n  filter: grayscale(0);\n  opacity: 1;\n}\n.initiatives-side__vote__voice .voteanim[data-v-320c7c50] {\n  filter: grayscale(0);\n}\n.initiatives-side__vote__voice.active .voteanim[data-v-320c7c50] {\n  display: none;\n}\n@keyframes leaves-320c7c50 {\n0% {\n    transform: scale(1);\n}\n100% {\n    transform: scale(1.15);\n}\n}\n.guide[data-v-320c7c50] {\n  display: block;\n  color: #007791;\n  margin-top: 15px;\n}\n.badge-success[data-v-320c7c50] {\n  background-color: #007791;\n}\n.over-images__item[data-v-320c7c50] {\n  width: 100%;\n  height: 140px;\n  object-fit: cover;\n  object-position: center;\n  border-radius: 4px;\n  overflow: hidden;\n  margin-bottom: 20px;\n  cursor: pointer;\n}\n', ""]), t.exports = o;
  },
  1269: function _(t, e, n) {
    "use strict";

    n.r(e);
    n(5), n(61), n(11), n(21), n(660), n(6), n(2), n(7), n(8);
    var o = n(0),
        i = (n(1), n(16), n(13), n(41), n(3), n(19), n(18), n(47), n(62), n(688)),
        a = (n(703), n(56));

    function s(t, e) {
      var n = Object.keys(t);

      if (Object.getOwnPropertySymbols) {
        var o = Object.getOwnPropertySymbols(t);
        e && (o = o.filter(function (e) {
          return Object.getOwnPropertyDescriptor(t, e).enumerable;
        })), n.push.apply(n, o);
      }

      return n;
    }

    function r(t) {
      for (var e = 1; e < arguments.length; e++) {
        var n = null != arguments[e] ? arguments[e] : {};
        e % 2 ? s(Object(n), !0).forEach(function (e) {
          Object(o.a)(t, e, n[e]);
        }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(n)) : s(Object(n)).forEach(function (e) {
          Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(n, e));
        });
      }

      return t;
    }

    var c = {
      name: "InitiativeById",
      components: {
        CoolLightBox: i.a
      },
      data: function data() {
        return {
          initDetail: {},
          warnTime: null,
          image_proxy: "https://openbudget.uz/api/v2/info/file/",
          isLoading: !1,
          voteWarn: !1,
          board_type: 1,
          votingStartDate: null,
          votingEndDate: null,
          is_voting_stage: !1,
          is_voting_btn: !0,
          waiting_stage: null,
          openLogin: !1,
          openVotes: !1,
          forgotLogin: !1,
          votes: 0,
          temp_votes: 0,
          offline_votes: 0,
          status: null,
          can_vote: !1,
          hasMap: !1,
          filter: {},
          items: [],
          files: [],
          imageItems: [],
          index: null,
          classification: null,
          cancel_classification: null,
          title: null,
          text: null,
          district: null,
          quarter: null,
          new_quarter: null,
          numberOfFiles: 0,
          budget: 0,
          is_winner: !1,
          public_id: 0,
          review_budget: 0,
          full_name: "",
          coords: [],
          votesList: [],
          paramsVote: {
            type: "",
            phone: "",
            offset: 0,
            limit: 10
          },
          countVotes: 0,
          application_review: null,
          statusList: {
            passed: {
              uz: "Тасдиқланди",
              ru: "Одобрена",
              qr: "Одобрена",
              oz: "Tasdiqlandi",
              en: "Approved"
            },
            not_passed: {
              uz: "Рад этилди",
              ru: "Отклонена",
              qr: "Отклонена",
              oz: "Rad etildi",
              en: "Rejected"
            },
            registered: {
              uz: "Рўйхатга олинди",
              ru: "Зарегистрировано",
              qr: "Зарегистрировано",
              oz: "Ro'yxatga olindi",
              en: "Registered"
            }
          },
          numberOfAllVotes: {
            uz: "Жами овозлaр сoни",
            ru: "Общее количество голосов",
            oz: "Jami ovozlar soni",
            en: "Total number of votes",
            qr: "Общее количество голосов"
          },
          onlineVotes: {
            uz: "Онлайн овозлар",
            ru: "Онлайн голосов",
            oz: "Onlayn ovozlar",
            en: "Online votes",
            qr: "Онлайн голосов"
          },
          smsVotes: {
            uz: "СМС орқали",
            ru: "Через СМС",
            oz: "SMS orqali",
            en: "SMS votes",
            qr: "Через СМС"
          },
          offlineVotes: {
            uz: "Офлайн овозлар",
            ru: "Офлайн голосов",
            oz: "Oflayn ovozlar",
            en: "Offline votes",
            qr: "Офлайн голосов"
          },
          sharing: {
            url: "https://openbudget.uz/",
            title: "",
            description: "",
            quote: ""
          },
          networks: [{
            network: "facebook",
            name: "Facebook",
            icon: "fa fa-facebook-f",
            color: "#1877f2"
          }, {
            network: "odnoklassniki",
            name: "Odnoklassniki",
            icon: "fa fa-odnoklassniki",
            color: "#ed812b"
          }, {
            network: "telegram",
            name: "Telegram",
            icon: "fa fa-paper-plane",
            color: "#0088cc"
          }],
          reason: "",
          isVoteType: !1,
          isVoteConfirm: !1,
          isVotePhone: !1,
          isVoteResponse: !1,
          smsToken: "",
          phone: "",
          isLoggedIn: !1,
          qullanma: n(705),
          score_set: null,
          adreska: "",
          isScoreInfo: !1,
          expert_openion: null,
          repairplannedroad: null,
          typeList: "all",
          voteInfoType: "success",
          isVoteLoader: !1,
          public_oversight: null,
          communityList: [{
            text: {
              uz: "Таклифим бўйича ханузгача ишлар бошланмади.",
              oz: "Taklifim bo‘yicha xanuzgacha ishlar boshlanmadi",
              ru: "По моему предложению работа еще не началась",
              en: "At my suggestion, work has not yet begun",
              qr: "По моему предложению работа еще не началась"
            },
            value: 1
          }, {
            text: {
              uz: "Таклифимни амалга ошириш бўйича ишлар давом этмоқда",
              oz: "Taklifimni amalga oshirish bo‘yicha ishlar davom etmoqda",
              ru: "Идет работа по реализации моего предложения",
              en: "Work is underway to implement my proposal",
              qr: "Идет работа по реализации моего предложения"
            },
            value: 2
          }, {
            text: {
              uz: " Таклифимда кўрсатилган ишлар бажарилди",
              oz: "Taklifimda ko‘rsatilgan ishlar bajarildi",
              ru: "Работа, изложенная в моем предложении, завершена.",
              en: "The work outlined in my proposal has been completed",
              qr: "Работа, изложенная в моем предложении, завершена."
            },
            value: 3
          }],
          coolItems: [],
          coolIndex: null
        };
      },
      mounted: function mounted() {
        this.fetch(), this.$store.dispatch("initiative/getInitCount", this.$route.params.initiativeId);
      },
      computed: r(r({}, Object(a.c)({
        voteCount: "initiative/voteCount"
      })), {}, {
        side_status: function side_status() {
          return this.statusList[this.status] && this.statusList[this.status][this.$i18n.locale] || this.status;
        },
        canBeVoted: function canBeVoted() {
          var t,
              e = new Date().getTime() >= new Date(this.votingStartDate).getTime() && new Date().getTime() <= new Date(this.votingEndDate).getTime();
          return !("PASSED" !== (null === (t = this.initDetail) || void 0 === t ? void 0 : t.stage) || !e);
        }
      }),
      methods: {
        submitVote: function submitVote() {
          this.pending = !0;
        },
        goBackOrToBoardList: function goBackOrToBoardList() {
          window.history.length > 2 ? this.$router.back() : this.$router.push("/boards-list/1");
        },
        voteWarning: function voteWarning(t) {
          this.isVotePhone = !1, this.voteWarn = !0, this.warnTime = t;
        },
        typesVote: function typesVote() {
          this.isVotePhone = !0;
        },
        smsVote: function smsVote() {
          localStorage.getItem("token") || (this.isVotePhone = !0);
        },
        phoneValidated: function phoneValidated(t) {
          var e = t.token,
              n = t.phone;
          this.smsToken = e, this.phone = n, this.isVotePhone = !1, this.isVoteConfirm = !0;
        },
        goToLogin: function goToLogin() {
          this.isVotePhone = !1, this.openLogin = !0;
        },
        close: function close() {
          this.isVoteType = !1, this.openLogin = !1, this.forgotLogin = !1, this.isVoteConfirm = !1, this.isVotePhone = !1, this.isVoteResponse = !1, this.voteWarn = !1, this.$store.dispatch("initiative/getInitCount", this.$route.params.initiativeId);
        },
        GetVotes: function GetVotes() {
          this.votesList = [], this.openVotes = !0;
        },
        tabVotesParams: function tabVotesParams(t) {
          this.paramsVote.limit = t.limit, this.paramsVote.offset = t.offset, this.GetVotes();
        },
        tabVotes: function tabVotes(t) {
          this.paramsVote.limit = 10, this.paramsVote.offset = 0, 1 == t ? (this.paramsVote.type = "sms", this.typeList = "sms") : 2 == t ? (this.paramsVote.type = "online", this.typeList = "online") : (this.paramsVote.type = "", this.typeList = "all"), this.GetVotes();
        },
        closeVotes: function closeVotes() {
          this.openVotes = !1, this.votesList = [], this.paramsVote.type = "";
        },
        forgot: function forgot() {
          this.forgotLogin = !0;
        },
        fetch: function fetch() {
          var t = this;
          this.isLoading || (this.isLoading = !0, this.$store.dispatch("initiative/getNewInitiativeById", {
            id: this.$route.params.initiativeId
          }).then(function (e) {
            t.initDetail = e, t.votingStartDate = e.votingStartDate, t.votingEndDate = e.votingEndDate, t.imageItems = e.images.map(function (e) {
              return "".concat(t.image_proxy).concat(e, "?type=LARGE");
            }), t.sharing.url += "boards/".concat(e.data.waiting_stage, "/").concat(e.data.id, "/"), t.sharing.title = e.data.title, t.sharing.description = e.data.text, t.sharing.quote = e.data.text, t.expert_openion = e.data.expert_openion, t.repairplannedroad = e.data.repairplannedroad, t.public_oversight = e.data.public_oversight;
          })["finally"](function () {
            t.isLoading = !1;
          }));
        },
        imageChecker: function imageChecker(t) {
          var e = t.split("."),
              n = e[e.length - 1];
          return "jpg" == n || "jpeg" == n || "png" == n || "svg" == n;
        },
        closeVote: function closeVote() {
          this.close();
        },
        okStatus: function okStatus() {
          this.close(), this.isVoteResponse = !0;
        },
        selectVoteTypeAuth: function selectVoteTypeAuth() {
          this.close(), this.openLogin = !0;
        },
        selectVoteTypeSms: function selectVoteTypeSms() {
          this.close(), this.isVotePhone = !0;
        },
        openScoreInfo: function openScoreInfo() {
          this.isScoreInfo = !0;
        },
        openScore: function openScore() {
          this.isScoreInfo = !0;
        },
        scoreGet: function scoreGet(t) {
          this.score_set = t;
        },
        closeScore: function closeScore() {
          this.isScoreInfo = !1;
        },
        openImageOversight: function openImageOversight() {
          this.coolItems = this.public_oversight && this.public_oversight.files || [];

          for (var t = 0; t < this.coolItems.length; t++) {
            this.coolItems[t].src = this.public_oversight && this.public_oversight.files && this.public_oversight.files[t].image;
          }

          this.coolIndex = 0;
        }
      },
      filters: {
        filter_id: function filter_id(t) {
          for (var e = t.toString(), n = 6 - e.split("").length, o = "", i = 0; i < n; i++) {
            o += "0";
          }

          return o += e;
        },
        filterSum: function filterSum(t) {
          var e = (t += "").split(".")[0].split("").reverse(),
              n = "";
          return e.forEach(function (t, e) {
            n += (e + 1) % 3 == 0 ? t + " " : t;
          }), n.split("").reverse().join("") + (t.split(".")[1] ? "." + t.split(".")[1] : "");
        },
        filterName: function filterName(t) {
          var e = t.split(""),
              n = "";
          return e.length > 11 ? (n = e.slice(0, 8).join(""), n += "...") : n = e.join(""), n;
        },
        splitDate: function splitDate(t) {
          var e = "";
          return t.includes("T") && (e = t.split("T")[0].split("-").reverse().join(".")), e;
        }
      }
    },
        l = (n(1141), n(15)),
        p = Object(l.a)(c, function () {
      var t,
          e,
          o,
          i,
          a,
          s,
          r,
          c,
          l,
          p,
          d,
          u,
          _,
          v,
          f,
          h,
          g,
          m,
          y,
          b,
          x,
          C,
          w,
          k,
          I = this,
          $ = I._self._c;

      return $("client-only", [$("section", {
        staticClass: "initiatives"
      }, [$("transition", {
        attrs: {
          name: "popup-fade",
          mode: "in-out"
        }
      }, [I.openLogin ? $("PopupLogin", {
        attrs: {
          close: I.close,
          forgot: I.forgot,
          voting: "",
          can_vote: I.fetch
        }
      }) : I.forgotLogin ? $("PopupForgot", {
        attrs: {
          close: I.close
        }
      }) : I._e()], 1), I._v(" "), $("transition", {
        attrs: {
          name: "popup-fade",
          mode: "in-out"
        }
      }, [I.openVotes ? $("PopupVotes", {
        attrs: {
          close: I.closeVotes,
          list: I.votesList
        },
        on: {
          tabVotes: I.tabVotes,
          tabVotesParams: I.tabVotesParams
        }
      }) : I._e()], 1), I._v(" "), I.isLoading ? $("div", {
        staticClass: "isLoaded-loader"
      }, [$("img", {
        directives: [{
          name: "lazy-load",
          rawName: "v-lazy-load"
        }],
        attrs: {
          "data-not-lazy": "",
          "data-src": n(656),
          alt: ""
        }
      })]) : $("b-container", {
        staticClass: "loading-in"
      }, [$("b-row", [$("b-col", {
        attrs: {
          xs: "12",
          sm: "12",
          md: "6",
          lg: "8"
        }
      }, [$("div", {
        staticClass: "d-flex"
      }, [$("a", {
        staticClass: "to-back cursor-pointer",
        on: {
          click: function click(t) {
            return t.preventDefault(), I.goBackOrToBoardList.apply(null, arguments);
          }
        }
      }, [I._v("\n              " + I._s(I.$t("objectCatalog")) + "\n            ")])]), I._v(" "), $("div", {
        staticClass: "initiatives-view"
      }, [$("div", {
        staticClass: "pages-title"
      }, [$("h2", [I._v("\n                " + I._s(null === (t = I.initDetail) || void 0 === t ? void 0 : t.categoryName) + "\n              ")])]), I._v(" "), $("div", {
        staticClass: "initiatives-view__maps"
      }, [$("p", [I._v("ID: " + I._s(null === (e = I.initDetail) || void 0 === e ? void 0 : e.publicId))])]), I._v(" "), $("div", {
        staticClass: "initiatives-view__content"
      }, [$("p", [I._v("\n                " + I._s(null === (o = I.initDetail) || void 0 === o ? void 0 : o.description) + "\n              ")])]), I._v(" "), $("div", {
        staticClass: "initiatives-view__images"
      }, [$("h3", [I._v("\n                " + I._s(I.$t("photo")) + ": (" + I._s((null === (i = I.initDetail) || void 0 === i || null === (a = i.images) || void 0 === a ? void 0 : a.length) || 0) + ")\n              ")]), I._v(" "), $("b-row", I._l(null === (s = I.initDetail) || void 0 === s ? void 0 : s.images, function (t, e) {
        return $("b-col", {
          key: e,
          attrs: {
            xs: "12",
            sm: "12",
            md: "6",
            lg: "4",
            cols: "6"
          }
        }, [$("a", {
          staticClass: "initiatives-view__images__item",
          attrs: {
            href: "#"
          },
          on: {
            click: function click(t) {
              t.preventDefault(), I.index = e;
            }
          }
        }, [$("img", {
          directives: [{
            name: "lazy-load",
            rawName: "v-lazy-load"
          }],
          attrs: {
            "data-src": I.image_proxy + t,
            alt: ""
          }
        })])]);
      }), 1)], 1), I._v(" "), I._e(), I._v(" "), I.hasMap ? $("div", {
        staticStyle: {
          height: "240px",
          "margin-bottom": "20px"
        }
      }, [$("CardsYandexMap", {
        attrs: {
          coordsProp: I.coords,
          clickable: ""
        }
      })], 1) : I._e(), I._v(" "), $("div", {
        staticClass: "sharing"
      }, I._l(I.networks, function (t) {
        return $("ShareNetwork", {
          key: t.network,
          style: {
            backgroundColor: t.color
          },
          attrs: {
            network: t.network,
            url: I.sharing.url,
            title: I.sharing.title || "",
            description: I.sharing.description,
            quote: I.sharing.quote,
            twitterUser: I.sharing.twitterUser
          }
        }, [$("i", {
          "class": t.icon
        })]);
      }), 1)]), I._v(" "), I.is_winner ? [$("CardsChronology", {
        attrs: {
          applicationId: I.$route.params.id
        },
        on: {
          openScore: I.openScore,
          scoreGet: I.scoreGet
        }
      })] : I._e()], 2), I._v(" "), $("b-col", {
        attrs: {
          xs: "12",
          sm: "12",
          md: "6",
          lg: "4"
        }
      }, [$("div", {
        staticClass: "initiatives-side"
      }, [$("div", {
        staticClass: "initiatives-side__status"
      }, [$("span", {
        "class": I.status
      }, [I._v("\n                " + I._s(I.$t("initiative"))), $("br"), I._v(I._s(I.is_winner ? I.$t("winner") : I.side_status) + "\n              ")])]), I._v(" "), $("span", {
        staticClass: "initiatives-side__district"
      }, [I._v("\n              " + I._s(null === (r = I.initDetail) || void 0 === r ? void 0 : r.regionName) + ", " + I._s(null === (c = I.initDetail) || void 0 === c ? void 0 : c.districtName) + "\n              " + I._s(null === (l = I.initDetail) || void 0 === l ? void 0 : l.quarterName) + "\n            ")]), I._v(" "), $("div", {
        staticClass: "pages-title"
      }, [$("h2", [I._v(I._s(I.title && I.title[I.$i18n.locale] || I.title))])]), I._v(" "), $("div", {
        staticClass: "initiatives-side__currency"
      }, [$("h3", [I._v(I._s(I.$t("amountOffered")))]), I._v(" "), $("span", [I._v(I._s(null === (p = I.initDetail) || void 0 === p || null === (d = p.requestedAmount) || void 0 === d ? void 0 : d.toLocaleString("en-US").replaceAll(",", " ")) + "\n                " + I._s(I.$t("som")))])]), I._v(" "), null !== (u = I.initDetail) && void 0 !== u && u.grantedAmount ? $("div", {
        staticClass: "initiatives-side__currency"
      }, [$("h3", [I._v(I._s(I.$t("granted_amount")))]), I._v(" "), $("span", [I._v("\n                " + I._s(null === (_ = I.initDetail) || void 0 === _ || null === (v = _.grantedAmount) || void 0 === v ? void 0 : v.toLocaleString("en-US").replaceAll(",", " ")) + "\n                " + I._s(I.$t("som")) + "\n              ")])]) : I._e(), I._v(" "), I.expert_openion && I.expert_openion.expert_budget ? $("div", {
        staticClass: "initiatives-side__currency"
      }, [$("h3", [I._v(I._s(I.$t("costOf")))]), I._v(" "), $("span", [I._v(I._s(I._f("filterSum")(I.expert_openion && I.expert_openion.expert_budget)) + "\n                " + I._s(I.$t("som")))]), I._v(" "), $("p", [$("a", {
        staticStyle: {
          "font-size": "16px",
          "font-weight": "600"
        },
        attrs: {
          href: I.expert_openion && I.expert_openion.expert_file
        }
      }, [$("b-icon", {
        attrs: {
          icon: "download"
        }
      }), I._v("\n                  " + I._s(I.$t("estimateDocs")))], 1)])]) : I.budget != I.review_budget && I.review_budget ? $("div", {
        staticClass: "initiatives-side__currency"
      }, [$("h3", [I._v(I._s(I.$t("costOf")))]), I._v(" "), $("span", [I._v(I._s(I._f("filterSum")(I.review_budget)) + " " + I._s(I.$t("som")))])]) : I._e(), I._v(" "), "NOT_PASSED" == (null === (f = I.initDetail) || void 0 === f ? void 0 : f.stage) ? $("p", [$("strong", {
        staticClass: "text-red"
      }, [I._v(I._s(I.$t("denied")))]), I._v(":\n              " + I._s(null === (h = I.initDetail) || void 0 === h ? void 0 : h.cancellationReasonTitle) + "\n              "), $("br"), I._v(" "), Array.isArray(null === (g = I.initDetail) || void 0 === g ? void 0 : g.cancellationReasonFiles) && null !== (m = I.initDetail) && void 0 !== m && m.cancellationReasonFiles.length ? $("a", {
        attrs: {
          href: I.image_proxy + "/" + (null === (y = I.initDetail) || void 0 === y ? void 0 : y.cancellationReasonFiles[0])
        }
      }, [$("svg", {
        staticClass: "bi-download b-icon bi",
        attrs: {
          viewBox: "0 0 16 16",
          width: "1em",
          height: "1em",
          focusable: "false",
          role: "img",
          "aria-label": "download",
          xmlns: "http://www.w3.org/2000/svg",
          fill: "currentColor"
        }
      }, [$("g", [$("path", {
        attrs: {
          d: "M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"
        }
      }), I._v(" "), $("path", {
        attrs: {
          d: "M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"
        }
      })])]), I._v("\n                " + I._s(I.$t("downloadFile")) + "\n              ")]) : I._e()]) : I._e(), I._v(" "), "NOT_PASSED" == (null === (b = I.initDetail) || void 0 === b ? void 0 : b.stage) ? $("p", [$("strong", {
        staticClass: "text-red"
      }, [I._v(I._s(I.$t("moderatorComment")) + ":")]), I._v("\n              " + I._s(null === (x = I.initDetail) || void 0 === x ? void 0 : x.moderatorComment) + "\n            ")]) : I._e(), I._v(" "), $("div", {
        staticClass: "initiatives-side__user d-flex w-100 justify-content-between"
      }, [$("div", [$("svg", {
        staticStyle: {
          "enable-background": "new 0 0 512 512"
        },
        attrs: {
          version: "1.1",
          id: "Capa_1",
          xmlns: "http://www.w3.org/2000/svg",
          "xmlns:xlink": "http://www.w3.org/1999/xlink",
          x: "0px",
          y: "0px",
          viewBox: "0 0 512 512",
          "xml:space": "preserve"
        }
      }, [$("g", [$("g", [$("path", {
        attrs: {
          d: "M256,0c-74.439,0-135,60.561-135,135s60.561,135,135,135s135-60.561,135-135S330.439,0,256,0z M256,240\n                                  c-57.897,0-105-47.103-105-105c0-57.897,47.103-105,105-105c57.897,0,105,47.103,105,105C361,192.897,313.897,240,256,240z"
        }
      })])]), I._v(" "), $("g", [$("g", [$("path", {
        attrs: {
          d: "M297.833,301h-83.667C144.964,301,76.669,332.951,31,401.458V512h450V401.458C435.397,333.05,367.121,301,297.833,301z\n                                  M451.001,482H451H61v-71.363C96.031,360.683,152.952,331,214.167,331h83.667c61.215,0,118.135,29.683,153.167,79.637V482z"
        }
      })])])]), I._v(" "), $("span", [I._v(I._s(null === (C = I.initDetail) || void 0 === C ? void 0 : C.authorFullName))])])]), I._v(" "), $("div", [I.reason ? $("div", [$("b", [I._v(I._s(I.$t("reason")))]), I._v(": "), $("br"), I._v("\n                " + I._s(I.reason) + "\n                "), $("b", [I._v("\n                  " + I._s(I.cancel_classification && I.cancel_classification.title && I.cancel_classification.title[I.$i18n.locale]) + "\n                ")])]) : $("div", [$("div", {
        staticClass: "initiatives-side__vote__info"
      }, [$("span", {
        staticClass: "vote-icons"
      }, [I._v(" " + I._s(I.$t("vote")) + ":")]), I._v(" "), $("div", {
        staticClass: "d-flex justify-content-around mb-5"
      }, [I.canBeVoted ? $("div", {
        staticClass: "initiatives-side__vote__voice mb-3"
      }, [$("a", {
        attrs: {
          target: "_blank",
          href: "https://t.me/ochiqbudjetbot?start=".concat(null === (w = I.initDetail) || void 0 === w ? void 0 : w.publicId)
        }
      }, [$("svg", {
        staticClass: "image",
        attrs: {
          id: "svg2",
          xmlns: "http://www.w3.org/2000/svg",
          viewBox: "0 0 240 240",
          width: "50",
          height: "50"
        }
      }, [$("linearGradient", {
        attrs: {
          id: "path2995-1-0_1_",
          gradientUnits: "userSpaceOnUse",
          x1: "-683.305",
          y1: "534.845",
          x2: "-693.305",
          y2: "511.512",
          gradientTransform: "matrix(6 0 0 -6 4255 3247)"
        }
      }, [$("stop", {
        attrs: {
          offset: "0",
          "stop-color": "#37aee2"
        }
      }), I._v(" "), $("stop", {
        attrs: {
          offset: "1",
          "stop-color": "#1e96c8"
        }
      })], 1), I._v(" "), $("path", {
        staticClass: "st0",
        attrs: {
          id: "path2995-1-0",
          d: "M240 120c0 66.3-53.7 120-120 120S0 186.3 0 120 53.7 0 120 0s120 53.7 120 120z"
        }
      }), I._v(" "), $("path", {
        staticClass: "st1",
        attrs: {
          id: "path2993",
          d: "M98 175c-3.9 0-3.2-1.5-4.6-5.2L82 132.2 152.8 88l8.3 2.2-6.9 18.8L98 175z"
        }
      }), I._v(" "), $("path", {
        staticClass: "st2",
        attrs: {
          id: "path2989",
          d: "M98 175c3 0 4.3-1.4 6-3 2.6-2.5 36-35 36-35l-20.5-5-19 12-2.5 30v1z"
        }
      }), I._v(" "), $("linearGradient", {
        attrs: {
          id: "path2991_1_",
          gradientUnits: "userSpaceOnUse",
          x1: "128.991",
          y1: "118.245",
          x2: "153.991",
          y2: "78.245",
          gradientTransform: "matrix(1 0 0 -1 0 242)"
        }
      }, [$("stop", {
        attrs: {
          offset: "0",
          "stop-color": "#eff7fc"
        }
      }), I._v(" "), $("stop", {
        attrs: {
          offset: "1",
          "stop-color": "#fff"
        }
      })], 1), I._v(" "), $("path", {
        staticClass: "st3",
        attrs: {
          id: "path2991",
          d: "M100 144.4l48.4 35.7c5.5 3 9.5 1.5 10.9-5.1L179 82.2c2-8.1-3.1-11.7-8.4-9.3L55 117.5c-7.9 3.2-7.8 7.6-1.4 9.5l29.7 9.3L152 93c3.2-2 6.2-.9 3.8 1.3L100 144.4z"
        }
      })], 1), I._v(" "), $("span", [I._v(I._s(I.$t("voteByTelegram")))])]), I._v(" "), $("img", {
        directives: [{
          name: "lazy-load",
          rawName: "v-lazy-load"
        }],
        staticClass: "voteanim",
        attrs: {
          "data-src": n(806),
          alt: ""
        }
      })]) : I._e(), I._v(" "), I.canBeVoted ? $("div", {
        staticClass: "initiatives-side__vote__voice"
      }, [$("a", {
        attrs: {
          href: "#"
        },
        on: {
          click: function click(t) {
            return t.preventDefault(), I.typesVote.apply(null, arguments);
          }
        }
      }, [$("img", {
        directives: [{
          name: "lazy-load",
          rawName: "v-lazy-load"
        }],
        staticClass: "image",
        attrs: {
          "data-src": n(1140),
          alt: ""
        }
      }), I._v(" "), $("span", [I._v(I._s(I.$t("voteBySms")))])]), I._v(" "), $("img", {
        directives: [{
          name: "lazy-load",
          rawName: "v-lazy-load"
        }],
        staticClass: "voteanim",
        attrs: {
          "data-src": n(806),
          alt: ""
        }
      })]) : I._e(), I._v(" "), I.canBeVoted ? $("div", {
        staticClass: "initiatives-side__vote__voice"
      }, [$("a", {
        attrs: {
          href: "https://play.google.com/store/apps/details?id=uz.minfin.open_budget",
          target: "_blank"
        }
      }, [$("svg", {
        staticClass: "image",
        attrs: {
          height: "50",
          viewBox: "0 -0.5 408 467.80000000000007",
          width: "50",
          xmlns: "http://www.w3.org/2000/svg"
        }
      }, [$("linearGradient", {
        attrs: {
          id: "a",
          gradientUnits: "userSpaceOnUse",
          x2: "261.746",
          y1: "112.094",
          y2: "112.094"
        }
      }, [$("stop", {
        attrs: {
          offset: "0",
          "stop-color": "#63be6b"
        }
      }), I._v(" "), $("stop", {
        attrs: {
          offset: ".506",
          "stop-color": "#5bbc6a"
        }
      }), I._v(" "), $("stop", {
        attrs: {
          offset: "1",
          "stop-color": "#4ab96a"
        }
      })], 1), I._v(" "), $("linearGradient", {
        attrs: {
          id: "b",
          gradientUnits: "userSpaceOnUse",
          x1: ".152",
          x2: "179.896",
          y1: "223.393",
          y2: "223.393"
        }
      }, [$("stop", {
        attrs: {
          offset: "0",
          "stop-color": "#3ec6f2"
        }
      }), I._v(" "), $("stop", {
        attrs: {
          offset: "1",
          "stop-color": "#45afe3"
        }
      })], 1), I._v(" "), $("linearGradient", {
        attrs: {
          id: "c",
          gradientUnits: "userSpaceOnUse",
          x1: "179.896",
          x2: "407.976",
          y1: "229.464",
          y2: "229.464"
        }
      }, [$("stop", {
        attrs: {
          offset: "0",
          "stop-color": "#faa51a"
        }
      }), I._v(" "), $("stop", {
        attrs: {
          offset: ".387",
          "stop-color": "#fab716"
        }
      }), I._v(" "), $("stop", {
        attrs: {
          offset: ".741",
          "stop-color": "#fac412"
        }
      }), I._v(" "), $("stop", {
        attrs: {
          offset: "1",
          "stop-color": "#fac80f"
        }
      })], 1), I._v(" "), $("linearGradient", {
        attrs: {
          id: "d",
          gradientUnits: "userSpaceOnUse",
          x1: "1.744",
          x2: "272.296",
          y1: "345.521",
          y2: "345.521"
        }
      }, [$("stop", {
        attrs: {
          offset: "0",
          "stop-color": "#ec3b50"
        }
      }), I._v(" "), $("stop", {
        attrs: {
          offset: "1",
          "stop-color": "#e7515b"
        }
      })], 1), I._v(" "), $("path", {
        attrs: {
          d: "M261.7 142.3L15 1.3C11.9-.5 8-.4 5 1.4c-3.1 1.8-5 5-5 8.6 0 0 .1 13 .2 34.4l179.7 179.7z",
          fill: "url(#a)"
        }
      }), I._v(" "), $("path", {
        attrs: {
          d: "M.2 44.4C.5 121.6 1.4 309 1.8 402.3L180 224.1z",
          fill: "url(#b)"
        }
      }), I._v(" "), $("path", {
        attrs: {
          d: "M402.9 223l-141.2-80.7-81.9 81.8 92.4 92.4L403 240.3c3.1-1.8 5-5.1 5-8.6 0-3.6-2-6.9-5.1-8.7z",
          fill: "url(#c)"
        }
      }), I._v(" "), $("path", {
        attrs: {
          d: "M1.7 402.3c.2 33.3.3 54.6.3 54.6 0 3.6 1.9 6.9 5 8.6 3.1 1.8 6.9 1.8 10 0l255.3-148.9-92.4-92.4z",
          fill: "url(#d)"
        }
      })], 1), I._v(" "), $("span", [I._v(I._s(I.$t("voteByApp")))])]), I._v(" "), $("img", {
        directives: [{
          name: "lazy-load",
          rawName: "v-lazy-load"
        }],
        staticClass: "voteanim",
        attrs: {
          "data-src": n(806),
          alt: ""
        }
      })]) : I._e()])]), I._v(" "), $("div", {
        staticClass: "initiatives-side__vote__info"
      }, [$("span", {
        staticClass: "mb-2"
      }, [I._v(I._s(I.numberOfAllVotes[I.$i18n.locale]) + " -\n                    "), $("b-badge", {
        attrs: {
          id: "tooltip_on",
          variant: "success"
        }
      }, [I._v(I._s(I.voteCount || 0))])], 1), I._v(" "), "PASSED" === (null === (k = I.initDetail) || void 0 === k ? void 0 : k.stage) ? $("button", {
        staticClass: "btn btn-primary mt-3 btn-block",
        on: {
          click: function click(t) {
            return I.GetVotes();
          }
        }
      }, [I._v("\n                    " + I._s(I.$t("votesShow")) + "\n                  ")]) : I._e()])])]), I._v(" "), I._e()]), I._v(" "), I.is_winner && I.public_oversight ? [$("div", {
        staticClass: "initiatives-side initiatives-side_community",
        staticStyle: {
          "min-height": "auto"
        }
      }, [$("div", {
        staticClass: "pages-title"
      }, [$("h2", [I._v("\n                  " + I._s(I.$t("publicOversightTitle")) + " "), $("br"), $("small", [I._v(I._s(I.full_name))])]), I._v(" "), I.public_oversight && 3 == I.public_oversight.offer_type ? $("img", {
        directives: [{
          name: "lazy-load",
          rawName: "v-lazy-load"
        }],
        attrs: {
          "data-src": n(676),
          alt: ""
        }
      }) : I.public_oversight && 2 == I.public_oversight.offer_type ? $("img", {
        directives: [{
          name: "lazy-load",
          rawName: "v-lazy-load"
        }],
        attrs: {
          "data-src": n(677),
          alt: ""
        }
      }) : I.public_oversight && 1 == I.public_oversight.offer_type ? $("img", {
        directives: [{
          name: "lazy-load",
          rawName: "v-lazy-load"
        }],
        attrs: {
          "data-src": n(678),
          alt: ""
        }
      }) : I._e()]), I._v(" "), $("div", {}, [$("p", [$("b", [I._v(I._s(I.$t("remainingTime")) + ":")]), I._v("\n                  " + I._s(I._f("splitDate")(I.public_oversight.created_at || "")) + "\n                ")]), I._v(" "), $("p", [$("b", [I._v(I._s(I.$t("itemStatus")) + ": ")]), I._v(I._s(1 == I.public_oversight.offer_type ? I.communityList[0].text[I.$i18n.locale] : 2 == I.public_oversight.offer_type ? I.communityList[1].text[I.$i18n.locale] : 3 == I.public_oversight.offer_type ? I.communityList[2].text[I.$i18n.locale] : "") + "\n                ")]), I._v(" "), 3 == I.public_oversight.offer_type && I.public_oversight.short_content ? $("p", [$("b", [I._v(I._s(I.$t("initiatorThink")) + ": ")]), I._v(I._s(I.public_oversight.short_content && I.public_oversight.short_content.title && I.public_oversight.short_content.title[I.$i18n.locale]) + "\n                ")]) : I._e(), I._v(" "), 3 == I.public_oversight.offer_type && I.public_oversight.files ? $("div", {
        staticClass: "over-images"
      }, [$("p", [$("b", [I._v(I._s(I.$t("photo")) + " (" + I._s(I.public_oversight.files.length || 0) + ")\n                    ")])]), I._v(" "), $("div", {
        staticClass: "row"
      }, I._l(I.public_oversight.files, function (t) {
        return $("div", {
          key: t.id,
          staticClass: "col-md-6"
        }, [$("img", {
          directives: [{
            name: "lazy-load",
            rawName: "v-lazy-load"
          }],
          staticClass: "over-images__item",
          attrs: {
            "data-src": t.image,
            alt: ""
          },
          on: {
            click: function click(e) {
              return I.openImageOversight(t.image);
            }
          }
        })]);
      }), 0)]) : I._e()])])] : I._e()], 2)], 1)], 1), I._v(" "), $("client-only", [$("CoolLightBox", {
        attrs: {
          items: I.imageItems,
          index: I.index
        },
        on: {
          close: function close(t) {
            I.index = null;
          }
        }
      })], 1), I._v(" "), $("client-only", [$("CoolLightBox", {
        attrs: {
          items: I.coolItems,
          index: I.coolIndex
        },
        on: {
          close: function close(t) {
            I.coolIndex = null;
          }
        }
      })], 1), I._v(" "), $("transition", {
        attrs: {
          name: "popup-fade",
          mode: "in-out"
        }
      }, [I.isVoteConfirm ? $("VoteConfirmCode", {
        attrs: {
          close: I.close,
          token: I.smsToken,
          phone: I.phone
        },
        on: {
          okStatus: I.okStatus
        }
      }) : I._e()], 1), I._v(" "), $("transition", {
        attrs: {
          name: "popup-fade",
          mode: "in-out"
        }
      }, [I.isVotePhone ? $("VotePhone", {
        attrs: {
          close: I.close,
          customSubmit: !0
        },
        on: {
          phoneValidated: I.phoneValidated,
          openLogin: I.goToLogin,
          warning: I.voteWarning,
          "custom:submit": I.submitVote,
          okStatus: I.okStatus
        }
      }) : I._e()], 1), I._v(" "), $("transition", [I.voteWarn ? $("VoteWarn", {
        attrs: {
          close: I.close,
          timer: I.warnTime
        }
      }) : I._e()], 1), I._v(" "), $("transition", {
        attrs: {
          name: "popup-fade",
          mode: "in-out"
        }
      }, [I.isVoteResponse ? $("VoteResponse", {
        attrs: {
          type: I.voteInfoType
        },
        on: {
          closeVote: I.closeVote
        }
      }) : I._e()], 1), I._v(" "), $("transition", {
        attrs: {
          name: "popup-fade",
          mode: "in-out"
        }
      }, [I.is_winner && I.isScoreInfo ? $("PopupScoreInfo", {
        attrs: {
          closeScore: I.closeScore,
          review_budget: I.review_budget || I.budget,
          organization: I.score_set && I.score_set.budgetscore_step && I.score_set.budgetscore_step.budget_score && I.score_set.budgetscore_step.budget_score.organization,
          money_transfer: I.score_set && I.score_set.money_transfer,
          contracts: I.score_set && I.score_set.contract_transaction_step && I.score_set.contract_transaction_step.contracts,
          transactions: I.score_set && I.score_set.contract_transaction_step && I.score_set.contract_transaction_step.transactions
        }
      }) : I._e()], 1)], 1)]);
    }, [], !1, null, "320c7c50", null);
    e["default"] = p.exports;
    installComponents(p, {
      PopupLogin: n(332)["default"],
      PopupForgot: n(333)["default"],
      PopupVotes: n(762)["default"],
      CardsYandexMap: n(663)["default"],
      CardsChronology: n(763)["default"],
      VoteConfirmCode: n(764)["default"],
      VotePhone: n(720)["default"],
      VoteWarn: n(765)["default"],
      VoteResponse: n(766)["default"],
      PopupScoreInfo: n(767)["default"]
    });
  },
  648: function _(t, e, n) {
    var o = n(650);
    o.__esModule && (o = o["default"]), "string" == typeof o && (o = [[t.i, o, ""]]), o.locals && (t.exports = o.locals);
    (0, n(37)["default"])("1ceb12d0", o, !0, {
      sourceMap: !1
    });
  },
  649: function _(t, e, n) {
    "use strict";

    n(648);
  },
  650: function _(t, e, n) {
    var o = n(36)(!1);
    o.push([t.i, '\n.pagination[data-v-4d156550] {\n  display: flex;\n  align-items: center;\n  justify-content: space-between;\n  padding: 0;\n  padding-top: 32px;\n  padding-bottom: 4px;\n}\n.pagination-controls[data-v-4d156550] {\n  display: flex;\n  align-items: center;\n  justify-content: space-between;\n}\n.pagination-limit[data-v-4d156550] {\n  margin-right: 24px;\n}\n.pagination-limit .btn.dropdown-toggle[data-v-4d156550] {\n  padding: 8px 16px;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n  min-width: 40px;\n  border: 0;\n  height: 40px;\n  color: #313131;\n  background: #ffffff;\n  border-radius: 4px;\n  box-shadow: 2px 0px 12px rgba(0, 0, 0, 0.1);\n  margin: 0 4px;\n}\n.pagination-limit .dropdown-toggle[data-v-4d156550]::after {\n  margin-left: 10px;\n}\n.pagination-limit span[data-v-4d156550] {\n  font-weight: 500;\n  font-size: 16px;\n  line-height: 24px;\n  color: #313131;\n}\n.pagination-items[data-v-4d156550] {\n  display: flex;\n  align-items: center;\n}\n.pagination-item[data-v-4d156550] {\n  padding: 8px 16px;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n  min-width: 40px;\n  border: 1px solid #dae0e2;\n  height: 40px;\n  background: #ffffff;\n  border-radius: 4px;\n  margin: 0 4px;\n}\n.pagination-item[data-v-4d156550]:not([disabled="disabled"]){\n  color: #007791;\n}\n.pagination-item.active[data-v-4d156550] {\n  background-color: #007791;\n  border-color: #007791;\n  color: #ffffff;\n}\n.pagination-info span[data-v-4d156550] {\n  font-weight: 500;\n  font-size: 16px;\n  line-height: 24px;\n  color: #313131;\n}\n@media(max-width: 991px) {\n.pagination-item[data-v-4d156550]{\n    padding: 4px 8px;\n    min-width: 30px;\n    height: 30px;\n    border-radius: 2px;\n    font-size: 13px;\n    margin: 0 2px;\n}\n.pagination-limit .dropdown-toggle[data-v-4d156550] {\n    padding: 4px 8px;\n    min-width: 30px;\n    height: 30px;\n    border-radius: 2px;\n    font-size: 13px;\n}\n.pagination-info span[data-v-4d156550] {\n    font-size: 14px;\n    line-height: 20px;\n}\n.pagination-limit[data-v-4d156550] {\n    margin-right: 8px;\n}\n.pagination[data-v-4d156550] {\n      padding-top: 16px;\n      padding-bottom: 0;\n}\n}\n@media(max-width: 576px) {\n.pagination[data-v-4d156550] {\n      flex-wrap: wrap;\n      justify-content: center;\n}\n.pagination-items[data-v-4d156550]{\n    flex-wrap: wrap;\n}\n.pagination-item[data-v-4d156550]{\n    margin-bottom: 4px;\n}\n}\n', ""]), t.exports = o;
  },
  652: function _(t, e, n) {
    "use strict";

    n.r(e);
    n(11), n(21), n(330);
    var o = {
      props: {
        count: {
          type: [String, Number],
          "default": function _default() {
            return 1;
          }
        },
        limit: {
          type: [String, Number],
          "default": function _default() {
            return 1;
          }
        },
        page: {
          type: [String, Number],
          "default": function _default() {
            return 1;
          }
        },
        countLimit: {
          type: Boolean,
          "default": !0
        }
      },
      data: function data() {
        return {
          limits: ["12", "16", "20", "24"],
          currentLimit: "12"
        };
      },
      computed: {
        currentPage: function currentPage() {
          return this.page || 1;
        },
        allPages: function allPages() {
          return Math.ceil(this.count / this.limit);
        }
      },
      mounted: function mounted() {
        this.currentLimit = this.limit + "";
      },
      watch: {},
      methods: {
        changeLimit: function changeLimit(t) {
          this.currentLimit = t, this.$emit("onChangeLimit", this.currentLimit), this.page = 1, this.currentPage = 1;
        },
        next: function next() {
          this.page++, this.currentPage++, this.currentPage <= this.allPages && this.$emit("paginate", this.currentPage);
        },
        prev: function prev() {
          this.page--, this.currentPage--, this.currentPage <= this.allPages && this.$emit("paginate", this.currentPage);
        },
        clickBtn: function clickBtn(t) {
          t <= this.allPages && (this.currentPage = t, this.$emit("paginate", t));
        }
      }
    },
        i = (n(649), n(15)),
        a = Object(i.a)(o, function () {
      var t,
          e = this,
          n = e._self._c;
      return n("div", {
        staticClass: "pagination"
      }, [n("div", {
        staticClass: "pagination-controls"
      }, [e.countLimit ? n("div", {
        staticClass: "pagination-limit"
      }, [n("b-dropdown", {
        attrs: {
          text: e.currentLimit
        }
      }, e._l(e.limits, function (t, o) {
        return n("b-dropdown-item", {
          key: o,
          on: {
            click: function click(n) {
              return e.changeLimit(t);
            }
          }
        }, [e._v(e._s(t))]);
      }), 1)], 1) : e._e(), e._v(" "), e.count ? n("div", {
        staticClass: "pagination-items"
      }, [n("button", {
        staticClass: "pagination-item",
        attrs: {
          disabled: !e.currentPage || 1 == e.currentPage
        },
        on: {
          click: e.prev
        }
      }, [e._v("<")]), e._v(" "), e._l(e.allPages, function (t) {
        return [1 == t || t == e.allPages || +e.currentPage - 2 <= t && +e.currentPage + 2 >= t ? n("button", {
          key: t,
          staticClass: "pagination-item",
          "class": {
            active: e.currentPage == t
          },
          attrs: {
            disabled: e.currentPage == t
          },
          on: {
            click: function click(n) {
              return e.clickBtn(t);
            }
          }
        }, [e._v(e._s(t))]) : e.currentPage + 2 < t && e.currentPage + 3 >= t || e.currentPage - 2 > t && e.currentPage - 3 <= t ? n("button", {
          staticClass: "pagination-item",
          attrs: {
            disabled: "ture"
          }
        }, [e._v("...")]) : e._e()];
      }), e._v(" "), n("button", {
        staticClass: "pagination-item",
        attrs: {
          disabled: !e.currentPage || e.currentPage >= e.allPages
        },
        on: {
          click: e.next
        }
      }, [e._v(">")])], 2) : e._e()]), e._v(" "), n("div", {
        staticClass: "pagination-info"
      }, [n("span", [e._v(e._s(e.$t("paginationTotal")) + ": " + e._s(null === (t = e.count) || void 0 === t ? void 0 : t.toLocaleString("en-US").replace(",", " ")))])])]);
    }, [], !1, null, "4d156550", null);
    e["default"] = a.exports;
  },
  654: function _(t, e, n) {
    var o = n(669);
    o.__esModule && (o = o["default"]), "string" == typeof o && (o = [[t.i, o, ""]]), o.locals && (t.exports = o.locals);
    (0, n(37)["default"])("74e5c4f0", o, !0, {
      sourceMap: !1
    });
  },
  656: function _(t, e, n) {
    t.exports = n.p + "img/loader.64152cf.svg";
  },
  663: function _(t, e, n) {
    "use strict";

    n.r(e);
    n(330);
    var o = n(667),
        i = {
      components: {
        yandexMap: o.a,
        ymapMarker: o.b
      },
      props: {
        coordsProp: {
          type: Array,
          defult: function defult() {
            return null;
          }
        },
        clickable: {
          type: Boolean,
          "default": function _default() {
            return !1;
          }
        },
        zoom: {
          type: [Number, String],
          "default": 10
        }
      },
      data: function data() {
        return {
          coords: [41.311144474609655, 69.27977555688582],
          settings: {
            apiKey: "",
            lang: "uz_UZ",
            coordorder: "latlong",
            version: "2.1"
          }
        };
      },
      computed: {
        balloonTemplate: function balloonTemplate() {
          return "\n        <p>".concat(this.coords, "</p>\n      ");
        }
      },
      methods: {
        onClick: function onClick(t) {
          this.clickable || (this.coords = t.get("coords"), this.$emit("change", this.coords));
        }
      },
      mounted: function mounted() {
        this.coordsProp && (this.coords = this.coordsProp);
      }
    },
        a = (n(668), n(15)),
        s = Object(a.a)(i, function () {
      var t = this,
          e = t._self._c;
      return e("yandex-map", {
        staticStyle: {
          width: "100%",
          height: "100%"
        },
        attrs: {
          coords: t.coords,
          settings: t.settings,
          zoom: t.zoom
        },
        on: {
          click: t.onClick
        }
      }, [e("ymap-marker", {
        attrs: {
          coords: t.coords,
          "marker-id": "123123",
          "balloon-template": t.balloonTemplate
        }
      })], 1);
    }, [], !1, null, null, null);
    e["default"] = s.exports;
  },
  668: function _(t, e, n) {
    "use strict";

    n(654);
  },
  669: function _(t, e, n) {
    var o = n(36)(!1);
    o.push([t.i, "\n.ymap-container {\n  height: 100%;\n}\n.red {\n  color: red;\n}\n", ""]), t.exports = o;
  },
  676: function _(t, e, n) {
    t.exports = n.p + "img/employee.c2ddcbc.png";
  },
  677: function _(t, e, n) {
    t.exports = n.p + "img/employee_yellow.02c62c6.png";
  },
  678: function _(t, e, n) {
    t.exports = n.p + "img/employee_red.7d440ff.png";
  },
  682: function _(t, e, n) {
    var o = n(707);
    o.__esModule && (o = o["default"]), "string" == typeof o && (o = [[t.i, o, ""]]), o.locals && (t.exports = o.locals);
    (0, n(37)["default"])("4653dfcf", o, !0, {
      sourceMap: !1
    });
  },
  691: function _(t, e, n) {
    var o = n(744);
    o.__esModule && (o = o["default"]), "string" == typeof o && (o = [[t.i, o, ""]]), o.locals && (t.exports = o.locals);
    (0, n(37)["default"])("3f4a821b", o, !0, {
      sourceMap: !1
    });
  },
  692: function _(t, e, n) {
    var o = n(746);
    o.__esModule && (o = o["default"]), "string" == typeof o && (o = [[t.i, o, ""]]), o.locals && (t.exports = o.locals);
    (0, n(37)["default"])("99929e88", o, !0, {
      sourceMap: !1
    });
  },
  693: function _(t, e, n) {
    var o = n(748);
    o.__esModule && (o = o["default"]), "string" == typeof o && (o = [[t.i, o, ""]]), o.locals && (t.exports = o.locals);
    (0, n(37)["default"])("d292f678", o, !0, {
      sourceMap: !1
    });
  },
  694: function _(t, e, n) {
    var o = n(750);
    o.__esModule && (o = o["default"]), "string" == typeof o && (o = [[t.i, o, ""]]), o.locals && (t.exports = o.locals);
    (0, n(37)["default"])("e6f31554", o, !0, {
      sourceMap: !1
    });
  },
  695: function _(t, e, n) {
    var o = n(752);
    o.__esModule && (o = o["default"]), "string" == typeof o && (o = [[t.i, o, ""]]), o.locals && (t.exports = o.locals);
    (0, n(37)["default"])("64148981", o, !0, {
      sourceMap: !1
    });
  },
  705: function _(t, e, n) {
    t.exports = n.p + "img/qullanma.03e0922.jpg";
  },
  706: function _(t, e, n) {
    "use strict";

    n(682);
  },
  707: function _(t, e, n) {
    var o = n(36)(!1);
    o.push([t.i, "\n.px-1[data-v-7008afd4]{\n  padding: 0 .25rem;\n  min-width: 140px;\n  text-align: center;\n  line-height: 45px;\n}\n.custom-spinner[data-v-7008afd4]{\n  margin: 0 auto!important;\n  width: 1.5rem!important;\n  height: 1.5rem!important;\n}\n.capcha-image[data-v-7008afd4]{\n  height: 45px;\n}\n.popup-body-error-message[data-v-7008afd4] {\n  margin: 0 10px;\n  color: red;\n  font-weight: 500;\n}\n.captcha-field[data-v-7008afd4] {\n  display: flex;\n  flex-direction: row;\n}\n", ""]), t.exports = o;
  },
  720: function _(t, e, n) {
    "use strict";

    n.r(e);
    n(47), n(62);
    var o = n(12),
        i = (n(45), n(330), n(11), n(21), n(18), n(1), n(42), n(41), n(2), n(3), n(165)),
        a = {
      name: "VotePhone",
      emits: ["closeModal"],
      props: {
        close: {
          type: Function,
          "default": function _default() {
            return null;
          }
        },
        list: {
          "default": []
        },
        applicationId: {
          type: [Number, String],
          "default": function _default() {
            return null;
          }
        },
        customSubmit: {
          type: Boolean,
          "default": !1
        },
        propsPending: {
          type: Boolean,
          "default": !1
        }
      },
      data: function data() {
        return {
          isPending: !1,
          phone: "",
          passcode: null,
          secret_key: null,
          captchaResult: null,
          captchaImage: null,
          captchaKey: null,
          otpKey: null,
          retryAfter: 0,
          retryAfterText: "",
          code: "",
          showAlert: !1,
          alertMsg: null,
          checkLoader: !1,
          warn: {
            status: !1,
            text: {
              qr: "Этот номер использовался в процессе голосования",
              uz: "Овоз бериш жараёнида бу рақамдан фойдаланилган",
              en: "This number was used during the voting process",
              oz: "Ovoz berish jarayonida bu raqamdan fodalanilgan",
              ru: "Этот номер использовался в процессе голосования"
            }
          },
          error: null,
          humansText: {
            qr: "Использование мобильных операторов HUMANS в процессе голосования запрещено!",
            uz: "Овоз бериш жараёнида HUMANS мобил операторларидан фойдалнишга рухсат этилмайди!",
            en: "The use of HUMANS mobile operators in the voting process is prohibited!",
            oz: "Ovoz berish jarayonida HUMANS mobil operatorlaridan foydalnishga ruxsat etilmaydi!",
            ru: "Использование мобильных операторов HUMANS в процессе голосования запрещено!"
          },
          stage: "phone"
        };
      },
      computed: {
        isValidHumans: function isValidHumans() {
          return !(this.phone.includes("+998 (33)") || this.phone.includes("+998(33)") || this.phone.includes("+99833"));
        },
        phoneFilled: function phoneFilled() {
          return this.phone && 19 == this.phone.length;
        },
        isValidPhoneNumber: function isValidPhoneNumber() {
          return 12 == this.phone.replace(/[^0-9]/g, "").length;
        }
      },
      methods: {
        getCaptchaImage: function getCaptchaImage() {
          var t = this;
          this.captchaImage = null, this.$mf_api.get("/vote/captcha-2", {
            headers: {
              "Access-Captcha": Object(i.a)()
            }
          }).then(function (e) {
            var n = e.data;
            localStorage.setItem("c", n.image), t.captchaKey = n.captchaKey, t.captchaImage = Object(i.b)(n), t.captchaResult = null;
          })["catch"](function (t) {});
        },
        checkVote: function checkVote() {
          var t = this;
          return Object(o.a)(regeneratorRuntime.mark(function e() {
            var n;
            return regeneratorRuntime.wrap(function (e) {
              for (;;) {
                switch (e.prev = e.next) {
                  case 0:
                    if (!t.isValidHumans) {
                      e.next = 9;
                      break;
                    }

                    return e.next = 3, t.$recaptcha.execute("checkVote");

                  case 3:
                    e.sent, n = {
                      captchaKey: t.captchaKey,
                      captchaResult: parseInt(t.captchaResult, 10),
                      phoneNumber: t.phone ? "998" + t.phone.replace(/[^a-zA-Z0-9]/g, "").slice(3) : null,
                      boardId: parseInt(t.$route.params.boardId, 10)
                    }, t.isPending = !0, t.$mf_api.post("/vote/check", n).then(function (e) {
                      var n = e.data;
                      t.stage = "code", t.otpKey = n.otpKey, t.retryAfter = n.retryAfter, t.startCount(), t.showAlert = !1, t.alertMsg = null;
                    })["catch"](function (e) {
                      var n = e.response;
                      t.showAlert = !0, t.alertMsg = 112 === n.data.code ? t.warn.text[t.$i18n.locale] : n.data.message, t.getCaptchaImage(), t.$bvToast.toast(n.data.message || "Amaliyotni bajarishda xatolik sodir bo'ldi.", {
                        title: "Xatolik",
                        variant: "danger",
                        autoHideDelay: 1e4
                      });
                    })["finally"](function () {
                      t.isPending = !1;
                    }), e.next = 11;
                    break;

                  case 9:
                    t.error = t.humansText[t.$i18n.locale], setTimeout(function () {
                      t.error = null, t.isPending = !1;
                    }, 5e3);

                  case 11:
                  case "end":
                    return e.stop();
                }
              }
            }, e);
          }))();
        },
        startCount: function startCount() {
          var t = this;
          setTimeout(function () {
            t.retryAfter > 0 ? (t.retryAfter = t.retryAfter - 1, t.retryAfterText = t.padWithZero(parseInt(t.retryAfter / 60, 10)) + " : " + t.padWithZero(parseInt(t.retryAfter % 60, 10)), t.startCount()) : t.retryAfterText = "";
          }, 1e3);
        },
        padWithZero: function padWithZero(t) {
          return t.toString().length < 2 ? "0" + t : t;
        },
        resendSms: function resendSms() {
          var t = this,
              e = {
            otpKey: this.otpKey
          };
          this.$mf_api.post("/vote/resend-sms", e).then(function (e) {
            var n = e.data;
            t.otpKey = n.otpKey, t.retryAfter = n.retryAfter, t.startCount();
          })["catch"](function (e) {
            var n = e.response;
            t.$bvToast.toast(n.data.message || "Amaliyotni bajarishda xatolik sodir bo'ldi.", {
              title: "Xatolik",
              variant: "danger",
              autoHideDelay: 1e4
            });
          });
        },
        verifyVote: function verifyVote() {
          var t = this;
          this.isPending = !0;
          var e = this.list.filter(function (t) {
            return t.selected;
          }),
              n = [];
          e.forEach(function (t) {
            n.push(t.id);
          });
          var o = {
            otpKey: this.otpKey,
            otpCode: this.code,
            initiativeId: this.$route.params.initiativeId,
            subinitiativesId: n
          };
          this.showAlert = !1, this.alertMsg = null, this.$mf_api.post("/vote/verify", o).then(function (e) {
            t.$emit("okStatus");
          })["catch"](function (e) {
            var n = e.response;
            t.showAlert = !0, t.alertMsg = n.data.message, t.$bvToast.toast(n.data.message || "Amaliyotni bajarishda xatolik sodir bo'ldi.", {
              title: "Xatolik",
              variant: "danger",
              autoHideDelay: 1e4
            });
          })["finally"](function () {
            t.isPending = !1;
          });
        }
      },
      mounted: function mounted() {
        var t = this;
        return Object(o.a)(regeneratorRuntime.mark(function e() {
          return regeneratorRuntime.wrap(function (e) {
            for (;;) {
              switch (e.prev = e.next) {
                case 0:
                  t.getCaptchaImage();

                case 1:
                case "end":
                  return e.stop();
              }
            }
          }, e);
        }))();
      }
    },
        s = (n(706), n(15)),
        r = Object(s.a)(a, function () {
      var t = this,
          e = t._self._c;
      return e("div", {
        staticClass: "popup"
      }, [e("div", {
        staticClass: "popup-main"
      }, [e("div", {
        staticClass: "popup-head popup-head__login"
      }, [e("h2", {
        staticClass: "popup-head__title"
      }, [t._v(t._s(t.$t("smsVote")))])]), t._v(" "), e("div", {
        staticClass: "popup-body"
      }, [e("form", {
        staticClass: "form",
        attrs: {
          action: ""
        }
      }, [t.warn.status ? e("p", {
        staticStyle: {
          color: "#f54141",
          "line-height": "17px"
        }
      }, [t._v("\n          " + t._s(t.warn.text[t.$i18n.locale]) + "\n        ")]) : t._e(), t._v(" "), t.showAlert ? e("b-alert", {
        attrs: {
          show: "",
          variant: "danger"
        }
      }, [t._v(t._s(t.alertMsg))]) : t._e(), t._v(" "), t.error ? e("div", {
        staticClass: "popup-body-error-message"
      }, [t._v("\n          " + t._s(t.error) + "\n        ")]) : t._e(), t._v(" "), e("b-form-group", {
        attrs: {
          label: t.$t("phone"),
          "label-for": "phone"
        }
      }, [e("b-form-input", {
        directives: [{
          name: "mask",
          rawName: "v-mask",
          value: "+998 (##) ###-##-##",
          expression: "'+998 (##) ###-##-##'"
        }],
        attrs: {
          id: "phone",
          placeholder: "+998(__) ___-__-__",
          type: "text",
          disabled: "phone" != t.stage
        },
        model: {
          value: t.phone,
          callback: function callback(e) {
            t.phone = e;
          },
          expression: "phone"
        }
      })], 1), t._v(" "), "phone" === t.stage ? e("b-form-group", {
        attrs: {
          label: t.$t("captchaText"),
          "label-for": "captchaResult"
        }
      }, [e("div", {
        staticClass: "captcha-field"
      }, [e("div", [e("b-button", {
        attrs: {
          disabled: "phone" != t.stage,
          variant: "outline-secondary"
        },
        on: {
          click: t.getCaptchaImage
        }
      }, [e("svg", {
        attrs: {
          width: "24",
          height: "24",
          viewBox: "0 0 24 24",
          fill: "none",
          xmlns: "http://www.w3.org/2000/svg"
        }
      }, [e("g", {
        attrs: {
          "clip-path": "url(#clip0_405_1548)"
        }
      }, [e("path", {
        attrs: {
          d: "M3.08615 10.6841C3.8103 5.76306 8.3866 2.36083 13.3076 3.08498C14.9866 3.33203 16.5615 4.04842 17.8511 5.15163L16.6902 6.31251C16.3011 6.7017 16.3012 7.33267 16.6905 7.72176C16.8773 7.90851 17.1306 8.01347 17.3948 8.01351H21.9647C22.515 8.01351 22.9611 7.56739 22.9611 7.01705V2.44715C22.961 1.89681 22.5148 1.45077 21.9644 1.45087C21.7003 1.45091 21.4469 1.55587 21.2601 1.74262L19.9646 3.03803C15.0245 -1.36557 7.44996 -0.930701 3.04635 4.00937C1.48628 5.75947 0.473881 7.92878 0.134578 10.2486C0.000799559 11.071 0.559034 11.8462 1.38141 11.98C1.4555 11.992 1.53033 11.9985 1.60539 11.9994C2.3578 11.9913 2.98937 11.4303 3.08615 10.6841Z",
          fill: "#374957"
        }
      }), t._v(" "), e("path", {
        attrs: {
          d: "M22.3939 11.9992C21.6415 12.0073 21.0099 12.5684 20.9131 13.3146C20.189 18.2356 15.6127 21.6378 10.6917 20.9137C9.0127 20.6666 7.43773 19.9503 6.14815 18.8471L7.30904 17.6862C7.69814 17.297 7.69804 16.666 7.30881 16.2769C7.12201 16.0902 6.86866 15.9852 6.60451 15.9852H2.03471C1.48437 15.9852 1.03824 16.4313 1.03824 16.9816V21.5515C1.03838 22.1019 1.4846 22.5479 2.03494 22.5478C2.29909 22.5478 2.55244 22.4428 2.73924 22.2561L4.03465 20.9606C8.97356 25.3647 16.5476 24.9312 20.9517 19.9922C22.5126 18.2418 23.5255 16.0717 23.8647 13.7511C23.999 12.9287 23.4413 12.1532 22.619 12.0189C22.5446 12.0067 22.4693 12.0001 22.3939 11.9992Z",
          fill: "#374957"
        }
      })]), t._v(" "), e("defs", [e("clipPath", {
        attrs: {
          id: "clip0_405_1548"
        }
      }, [e("rect", {
        attrs: {
          width: "24",
          height: "24",
          fill: "white"
        }
      })])])])])], 1), t._v(" "), t.captchaImage ? e("div", [e("img", {
        directives: [{
          name: "lazy-load",
          rawName: "v-lazy-load"
        }],
        staticClass: "capcha-image",
        attrs: {
          "data-src": "data:image/png;base64, ".concat(t.captchaImage)
        }
      })]) : e("div", {
        staticClass: "px-1"
      }, [e("b-spinner", {
        staticClass: "custom-spinner"
      })], 1), t._v(" "), e("div", [e("b-form-input", {
        directives: [{
          name: "mask",
          rawName: "v-mask",
          value: "######",
          expression: "'######'"
        }],
        attrs: {
          id: "captchaResult",
          placeholder: t.$t("captchaResult"),
          type: "text",
          disabled: "phone" != t.stage
        },
        model: {
          value: t.captchaResult,
          callback: function callback(e) {
            t.captchaResult = e;
          },
          expression: "captchaResult"
        }
      })], 1)])]) : t._e(), t._v(" "), "code" === t.stage ? e("b-form-group", {
        attrs: {
          label: t.$t("smsVoteConfirm"),
          "label-for": "code"
        }
      }, [e("b-form-input", {
        directives: [{
          name: "mask",
          rawName: "v-mask",
          value: "######",
          expression: "'######'"
        }],
        attrs: {
          id: "code",
          placeholder: "",
          type: "text",
          disabled: "code" != t.stage
        },
        model: {
          value: t.code,
          callback: function callback(e) {
            t.code = e;
          },
          expression: "code"
        }
      }), t._v(" "), e("b-button", {
        attrs: {
          variant: "link",
          disabled: t.retryAfter > 0
        },
        on: {
          click: t.resendSms
        }
      }, [t._v("\n            " + t._s(t.$t("resendSms")) + "\n            "), e("span", [t._v(t._s(t.retryAfterText))])])], 1) : t._e(), t._v(" "), t.customSubmit ? e("b-button-group", ["phone" === t.stage ? e("b-button", {
        attrs: {
          disabled: t.isPending || t.propsPending || !t.phoneFilled || !t.captchaResult
        },
        on: {
          click: function click(e) {
            return t.checkVote();
          }
        }
      }, [t.isPending ? e("b-spinner", {
        staticClass: "custom-spinner"
      }) : t._e(), t._v("\n            СМС кодни юбориш\n          ")], 1) : t._e(), t._v(" "), "code" === t.stage ? e("b-button", {
        attrs: {
          disabled: t.isPending || t.propsPending || t.code.length < 6 || t.code && t.code.includes("#") || !t.phoneFilled
        },
        on: {
          click: function click(e) {
            return t.verifyVote();
          }
        }
      }, [t.isPending ? e("b-spinner", {
        staticClass: "custom-spinner"
      }) : t._e(), t._v(" " + t._s(t.$t("send")) + "\n          ")], 1) : t._e()], 1) : e("b-button-group", [e("b-button", {
        attrs: {
          disabled: t.isPending || t.isValidHumans || !t.phoneFilled
        },
        on: {
          click: t.pass
        }
      }, [t.isPending ? e("b-spinner", {
        staticClass: "custom-spinner"
      }) : t._e(), t._v("\n            " + t._s(t.$t("send")) + "\n          ")], 1)], 1)], 1)]), t._v(" "), e("span", {
        staticClass: "popup-main__close"
      }, [e("b-icon", {
        attrs: {
          icon: "x"
        },
        on: {
          click: t.close
        }
      })], 1)])]);
    }, [], !1, null, "7008afd4", null);
    e["default"] = r.exports;
  },
  743: function _(t, e, n) {
    "use strict";

    n(691);
  },
  744: function _(t, e, n) {
    var o = n(36)(!1);
    o.push([t.i, "\n.forgot[data-v-397c804f] {\n  cursor: pointer;\n}\n.forgot[data-v-397c804f]:hover {\n  color: #007791;\n}\n.popup-main.popup-main--votes[data-v-397c804f] {\n  max-width: 900px;\n}\n.popup-main.popup-main--votes .intable[data-v-397c804f] {\n  height: 480px;\n  overflow: auto;\n}\n.table-scroll-tr[data-v-397c804f] {\n  overflow-y: auto;\n}\n.popup--votes .d-flex[data-v-397c804f] {\n  align-items: center;\n}\n.votes-search[data-v-397c804f] {\n  max-width: 200px;\n  margin-left: auto;\n  margin-right: 15px;\n}\n.popup--votes .nav-tabs .nav-link[data-v-397c804f] {\n  color: #000;\n}\n.popup--votes .nav-tabs .nav-link.active[data-v-397c804f] {\n  color: rgb(0, 119, 145);\n}\n.intable table thead tr th[data-v-397c804f],\n.intable table tbody tr td[data-v-397c804f] {\n  white-space: nowrap;\n}\n.vote-table[data-v-397c804f]{\n  position: relative;\n}\n.table-loader[data-v-397c804f]{\n  position: absolute;\n  left: 0;\n  right: 0;\n  top: 0;\n  bottom: 0;\n  background-color: rgba(255,255,255,.2);\n  backdrop-filter: blur(4px);\n  display: flex;\n  justify-content: center;\n  align-items: center;\n}\n@media (max-width: 1440px) {\n.intable[data-v-397c804f] {\n    height: 400px;\n    overflow: auto;\n}\n.popup-main.popup-main--votes .nav[data-v-397c804f] {\n    flex-wrap: unset;\n    white-space: nowrap;\n    overflow-x: auto;\n}\n}\n", ""]), t.exports = o;
  },
  745: function _(t, e, n) {
    "use strict";

    n(692);
  },
  746: function _(t, e, n) {
    var o = n(36)(!1);
    o.push([t.i, '.chronology[data-v-4607d06f]{min-height:200px;padding:30px 0}.chronology-item[data-v-4607d06f]{position:relative;padding-left:24px}.chronology-item h3[data-v-4607d06f]{margin-bottom:8px;font-size:19px;line-height:1;font-weight:600;text-transform:uppercase}.chronology-item[data-v-4607d06f]::after{content:"";position:absolute;background:#fff;top:3px;left:5px;width:16px;height:16px;border-radius:50%;border:2px solid #dedede}.chronology-item[data-v-4607d06f]::before{content:"";position:absolute;width:2px;height:calc(100% - 24px);left:12px;top:18px;bottom:12px;background:#dedede}.chronology-item__info[data-v-4607d06f]{border-bottom-left-radius:6px;border-bottom-right-radius:6px;background:#fff;box-shadow:0px 2px 15px rgba(59,69,83,.02)}.chronology-info__item[data-v-4607d06f]{border-bottom:1px solid #dedede;pointer-events:none;opacity:.4}.chronology-info__item[data-v-4607d06f]:last-child{border-bottom:0}.chronology-info__head[data-v-4607d06f]{position:relative;padding:12px 16px 12px 40px;cursor:pointer}.chronology-info__head.active[data-v-4607d06f],.chronology-info__head[data-v-4607d06f]:hover{background:#f0fcff}.chronology-info__head span[data-v-4607d06f]{font-size:17px;font-weight:500}.chronology-info__head[data-v-4607d06f]::before{content:"";position:absolute;left:16px;bottom:50%;transform:translateY(50%) rotateZ(45deg);width:14px;height:14px;border:1px solid #ccc;border-radius:3px}.chronology-info__head.active[data-v-4607d06f]::before{background:#007791;border-color:#007791}.chronology-info__body[data-v-4607d06f]{padding:16px 32px;font-size:15px}.chronology-info__body p[data-v-4607d06f]:last-child{margin-bottom:0}.chronology-info__item.active[data-v-4607d06f]{pointer-events:all;opacity:1}.chronology-info__item.active .chronology-info__head[data-v-4607d06f]::before{border-color:#007791}.chronology-info__item .chronology-info__head span>i[data-v-4607d06f]{display:none}.chronology-info__item.active .chronology-info__head span>i[data-v-4607d06f]{display:inline-block;color:#19a100}', ""]), t.exports = o;
  },
  747: function _(t, e, n) {
    "use strict";

    n(693);
  },
  748: function _(t, e, n) {
    var o = n(36)(!1);
    o.push([t.i, "\n.form .btn-group .btn.btn-close[data-v-39f1eba2] {\n  background-color: #b9b9b9;\n  border-color: #bdbdbd;\n}\n.foot-text[data-v-39f1eba2]{\n  font-size: 15px;\n  display:block;\n  line-height: 17px;\n}\n.foot-red[data-v-39f1eba2]{\n  color: #f54141;\n}\n", ""]), t.exports = o;
  },
  749: function _(t, e, n) {
    "use strict";

    n(694);
  },
  750: function _(t, e, n) {
    var o = n(36)(!1);
    o.push([t.i, "\n.popup-main--excitedNumber[data-v-57ce72d2] {\n  min-height: 120px;\n  display: flex;\n  align-items: center;\n  text-align: center;\n  padding: 10px 30px 0px;\n}\n.popup-main--excitedNumber .popup-head__title[data-v-57ce72d2] {\n  width: 100%;\n  font-size: 23px;\n  line-height: 26px;\n  text-align: center;\n}\n.popup-main--excitedNumber .popup-head__title.success[data-v-57ce72d2]{\n  color: #3ca719;\n}\n.popup-main--excitedNumber .popup-head__title.error[data-v-57ce72d2]{\n  color: red;\n}\n", ""]), t.exports = o;
  },
  751: function _(t, e, n) {
    "use strict";

    n(695);
  },
  752: function _(t, e, n) {
    var o = n(36)(!1);
    o.push([t.i, "\n.popup-main--scoreInfo[data-v-29841440] {\n    max-width: 1200px;\n}\n.scoreInfo-tabs[data-v-29841440]{\n    display: flex;\n    align-items: center;\n    padding-bottom: 15px;\n}\n.scoreInfo-tab[data-v-29841440]{\n    border: 1px solid #007791;\n    height: 40px;\n    color: #007791;\n    display: flex;\n    align-items: center;\n    justify-content: center;\n    border-radius: 6px;\n    font-size: 15px;\n    line-height: 15px;\n    margin-right: 10px;\n    outline:none;\n    box-shadow: none;\n}\n.scoreInfo-tab.active[data-v-29841440]{\n    background: #007791;\n    color: #ffffff;\n}\n.infotable[data-v-29841440]{\n    font-size: 15px;\n}\n.infotable table > div[data-v-29841440]{\n    padding: 5px 15px;\n}\n.nowrap[data-v-29841440]{\n    white-space: nowrap;\n}\n.popup-main--scoreInfo .intable[data-v-29841440]{\n    max-height: 480px;\n    overflow-y: auto;\n}\n", ""]), t.exports = o;
  },
  762: function _(t, e, n) {
    "use strict";

    n.r(e);
    n(1);
    var o = {
      props: {
        close: {
          type: Function,
          "default": function _default() {
            return null;
          }
        },
        list: {
          type: Array,
          "default": function _default() {
            return [];
          }
        }
      },
      data: function data() {
        return {
          page: 0,
          last: !0,
          size: 10,
          phone: "",
          tabIndex: 0,
          votesList: [],
          loader: !1,
          limit: 10,
          tableLoader: !1
        };
      },
      mounted: function mounted() {
        var t = this;
        this.loader = !0, this.$store.dispatch("initiative/getVotesById", {
          id: this.$route.params.initiativeId,
          params: {
            size: this.size,
            page: this.page
          }
        }).then(function (e) {
          t.tabIndex = e.data.totalElements, t.last = e.data.last, t.votesList = e.data.content;
        })["finally"](function () {
          t.loader = !1;
        });
      },
      watch: {
        page: function page(t) {
          var e = this;
          this.tableLoader = !0, this.$store.dispatch("initiative/getVotesById", {
            id: this.$route.params.initiativeId,
            params: {
              size: this.size,
              page: t
            }
          }).then(function (t) {
            e.tabIndex = t.data.totalElements, e.last = t.data.last, e.votesList = t.data.content;
          })["finally"](function () {
            e.tableLoader = !1;
          });
        }
      },
      methods: {
        onChangeLimit: function onChangeLimit(t) {
          this.limit = t, this.$emit("tabVotesParams", this.params);
        },
        paginate: function paginate(t) {
          this.page = t - 1, this.$emit("tabVotesParams", this.params);
        },
        increasePageNum: function increasePageNum() {
          this.page += 1;
        },
        decreasePageNum: function decreasePageNum() {
          this.page -= 1;
        }
      },
      computed: {
        currentPage: function currentPage() {
          return this.page;
        },
        params: function params() {
          return {
            limit: this.limit || 10,
            offset: ((this.page || 1) - 1) * (this.limit || 10)
          };
        }
      },
      filters: {
        splitDate: function splitDate(t) {
          var e,
              n,
              o = "";
          return e = (o = t.split("T"))[0], n = o[1].split(".")[0], o = e + " " + n;
        }
      }
    },
        i = (n(743), n(15)),
        a = Object(i.a)(o, function () {
      var t,
          e = this,
          n = e._self._c;
      return n("div", {
        staticClass: "popup popup--votes"
      }, [n("div", {
        staticClass: "popup-main popup-main--votes"
      }, [n("div", {
        staticClass: "popup-head popup-head__login"
      }, [n("div", {
        staticClass: "d-flex"
      }, [n("h2", {
        staticClass: "popup-head__title"
      }, [e._v(e._s(e.$t("votesShow")))])])]), e._v(" "), e.loader ? n("div", {
        staticClass: "text-center py-3"
      }, [n("b-spinner")], 1) : n("div", {
        staticClass: "vote-table"
      }, [e.votesList.length ? n("table", {
        staticClass: "table table-striped"
      }, [n("thead", [n("tr", [n("th", [e._v(e._s(e.$t("tableIndex")))]), e._v(" "), n("th", [e._v(e._s(e.$t("phone")))]), e._v(" "), n("th", [e._v(e._s(e.$t("date")))])])]), e._v(" "), n("tbody", {
        staticClass: "w-100"
      }, e._l(e.votesList, function (t, o) {
        return n("tr", {
          key: o
        }, [n("td", [e._v("\n              " + e._s(0 === e.page ? o + 1 : o + 1 + 10 * e.page) + "\n            ")]), e._v(" "), n("td", [e._v("\n              " + e._s(t.phoneNumber) + "\n            ")]), e._v(" "), n("td", [e._v("\n              " + e._s(null == t ? void 0 : t.voteDate) + "\n            ")])]);
      }), 0)]) : n("div", {
        staticClass: "text-center text-muted border rounded py-5"
      }, [e._v("\n        " + e._s(e.$t("empty")) + "\n      ")]), e._v(" "), e.tableLoader ? n("div", {
        staticClass: "table-loader"
      }, [n("div", [n("b-spinner")], 1)]) : e._e()]), e._v(" "), n("div", {
        staticClass: "text-right mt-3"
      }, [Array.isArray(this.votesList) && (null === (t = this.votesList) || void 0 === t ? void 0 : t.length) > 0 ? n("UtilsPagination", {
        attrs: {
          limit: e.limit,
          count: e.tabIndex,
          page: e.page + 1
        },
        on: {
          onChangeLimit: e.onChangeLimit,
          paginate: e.paginate
        }
      }) : e._e()], 1), e._v(" "), n("span", {
        staticClass: "popup-main__close",
        on: {
          click: e.close
        }
      }, [n("b-icon", {
        attrs: {
          icon: "x"
        }
      })], 1)])]);
    }, [], !1, null, "397c804f", null);
    e["default"] = a.exports;
    installComponents(a, {
      UtilsPagination: n(652)["default"]
    });
  },
  763: function _(t, e, n) {
    "use strict";

    n.r(e);
    n(5), n(61);
    var o = n(12),
        i = (n(45), n(330), n(163), n(1), n(3), n(19), {
      props: {
        applicationId: {
          type: [Number, String],
          "default": function _default() {
            return null;
          }
        }
      },
      data: function data() {
        return {
          chronologyInfo: null,
          chronology: [!1, !1, !1, !1, !1, !1, !1, !1, !1, !1, !1, !1]
        };
      },
      mounted: function mounted() {
        var t = this;
        return Object(o.a)(regeneratorRuntime.mark(function e() {
          return regeneratorRuntime.wrap(function (e) {
            for (;;) {
              switch (e.prev = e.next) {
                case 0:
                  return e.next = 2, t.fetchChronology();

                case 2:
                  t.$emit("scoreGet", t.chronologyInfo);

                case 3:
                case "end":
                  return e.stop();
              }
            }
          }, e);
        }))();
      },
      methods: {
        fetchChronology: function fetchChronology() {
          var t = this;
          return Object(o.a)(regeneratorRuntime.mark(function e() {
            var n;
            return regeneratorRuntime.wrap(function (e) {
              for (;;) {
                switch (e.prev = e.next) {
                  case 0:
                    return e.next = 2, t.$store.dispatch("initiative/fetchChronology", {
                      id: t.applicationId
                    });

                  case 2:
                    n = e.sent, t.chronologyInfo = n && n.data;

                  case 4:
                  case "end":
                    return e.stop();
                }
              }
            }, e);
          }))();
        },
        openScoreInfo: function openScoreInfo() {
          this.$emit("openScore");
        },
        clickChronology: function clickChronology(t) {
          this.chronology[t] ? this.$set(this.chronology, t, !1) : this.$set(this.chronology, t, !0);
        }
      },
      filters: {
        filterSum: function filterSum(t) {
          t = (+t).toFixed(2);
          var e = (t += "").split(".")[0].split("").reverse(),
              n = "";
          return e.forEach(function (t, e) {
            n += (e + 1) % 3 == 0 ? t + " " : t;
          }), n.split("").reverse().join("") + (t.split(".")[1] ? "." + t.split(".")[1] : "");
        },
        dateFormat: function dateFormat(t) {
          return t.split("-").reverse().join(".");
        }
      }
    }),
        a = (n(745), n(15)),
        s = Object(a.a)(i, function () {
      var t = this,
          e = t._self._c;
      return e("div", {
        staticClass: "chronology"
      }, [e("div", {
        staticClass: "pages-title"
      }, [e("h2", [t._v(t._s(t.$t("chronologyTitle")))])]), t._v(" "), e("div", {
        staticClass: "chronology-item__info"
      }, [e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.type
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[0]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(0);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologyType")))])]), t._v(" "), t.chronology[0] ? e("div", {
        staticClass: "chronology-info__body"
      }, [t.chronologyInfo && t.chronologyInfo.type ? e("p", [e("b", [t._v(t._s(t.$t("chronologyType")) + ": ")]), t._v(" " + t._s(1 == t.chronologyInfo.type ? t.$t("chronologyType1") : 2 == t.chronologyInfo.type ? t.$t("chronologyType2") : 3 == t.chronologyInfo.type ? t.$t("chronologyType3") : t.$t("chronologyType4")) + "\n                    ")]) : t._e()]) : t._e()]), t._v(" "), t.chronologyInfo && 4 != t.chronologyInfo.type ? e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.adreska_step
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[1]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(1);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologyAdreskaStep")))])]), t._v(" "), t.chronology[1] ? e("div", {
        staticClass: "chronology-info__body"
      }, [t.chronologyInfo && t.chronologyInfo.adreska_step ? e("p", [e("b", [t._v(t._s(t.$t("reportWinsAdreska")) + ": ")]), t._v(" " + t._s(t.$t("chronologyAdreskaStep")) + "("), e("a", {
        attrs: {
          href: t.chronologyInfo.adreska_step.adreska && t.chronologyInfo.adreska_step.adreska.file,
          download: "download",
          target: "_blank"
        }
      }, [t._v(t._s(t.$t("downloadFile")))]), t._v(")\n                    ")]) : t._e()]) : t._e()]) : t._e(), t._v(" "), t.chronologyInfo && 4 != t.chronologyInfo.type ? e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.budgetscore_step
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[2]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(2);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologyBudgetScoreStep")))])]), t._v(" "), t.chronology[2] ? e("div", {
        staticClass: "chronology-info__body"
      }, [e("p", [e("b", [t._v(t._s(t.$t("reportWinsCheck")) + ": ")]), t._v("\n                        " + t._s(t.chronologyInfo && t.chronologyInfo.budgetscore_step && t.chronologyInfo.budgetscore_step.budget_score && t.chronologyInfo.budgetscore_step.budget_score.score || "") + "\n                    ")]), t._v(" "), e("p", [e("b", [t._v(t._s(t.$t("reportWinsInn")) + ": ")]), t._v("\n                        " + t._s(t.chronologyInfo && t.chronologyInfo.budgetscore_step && t.chronologyInfo.budgetscore_step.budget_score && t.chronologyInfo.budgetscore_step.budget_score.identification_number || "") + "\n                    ")]), t._v(" "), e("p", [e("b", [t._v(t._s(t.$t("reportWinsOrg")) + ":")]), t._v("\n                        " + t._s(t.chronologyInfo && t.chronologyInfo.budgetscore_step && t.chronologyInfo.budgetscore_step.budget_score && t.chronologyInfo.budgetscore_step.budget_score.organization || "") + "\n                    ")])]) : t._e()]) : t._e(), t._v(" "), t.chronologyInfo && 4 != t.chronologyInfo.type ? e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.money_transfer
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[3]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(3);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologyMoneyTransferStep")))])]), t._v(" "), t.chronology[3] ? e("div", {
        staticClass: "chronology-info__body"
      }, [e("p", [e("b", [t._v(t._s(t.$t("scoreTableTitle2")) + ":")]), t._v(" \n                        " + t._s(t._f("filterSum")(t.chronologyInfo && t.chronologyInfo.money_transfer && t.chronologyInfo.money_transfer.financed || 0)) + " (сўм)\n                    ")]), t._v(" "), e("p", [e("b", [t._v(t._s(t.$t("scoreTableTitle3")) + ":")]), t._v(" \n                        " + t._s(t._f("filterSum")(t.chronologyInfo && t.chronologyInfo.money_transfer && t.chronologyInfo.money_transfer.cost || 0)) + " (сўм)\n                    ")]), t._v(" "), e("p", [e("b", [t._v(t._s(t.$t("scoreTableTitle4")) + ":")]), t._v(" \n                        " + t._s(t._f("filterSum")(t.chronologyInfo && t.chronologyInfo.money_transfer && t.chronologyInfo.money_transfer.residue || 0)) + " (сўм)\n                    ")])]) : t._e()]) : t._e(), t._v(" "), t.chronologyInfo && 4 != t.chronologyInfo.type && 1 == t.chronologyInfo.type ? e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.announce_projector_step
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[4]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(4);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologyAnnounceProjectorStep")))])]), t._v(" "), t.chronology[4] ? e("div", {
        staticClass: "chronology-info__body"
      }, [e("p", [e("b", [t._v(t._s(t.$t("chronologyAdDate")) + ":")]), t._v(" \n                        " + t._s(t._f("dateFormat")(t.chronologyInfo && t.chronologyInfo.announce_projector_step && t.chronologyInfo.announce_projector_step.date)) + "\n                    ")]), t._v(" "), e("p", [e("b", [t._v(t._s(t.$t("chronologyAdLotNumber")) + ":")]), t._v(" \n                        " + t._s(t.chronologyInfo && t.chronologyInfo.announce_projector_step && t.chronologyInfo.announce_projector_step.lot_number || "") + "\n                    ")])]) : t._e()]) : t._e(), t._v(" "), t.chronologyInfo && 4 != t.chronologyInfo.type && 1 == t.chronologyInfo.type ? e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.smeta_protocol_step
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[7]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(7);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologySmetaProtocolStep")))])]), t._v(" "), t.chronology[7] ? e("div", {
        staticClass: "chronology-info__body"
      }, [e("p", [e("b", [t._v(t._s(t.$t("reportWinsAdreska")) + ": ")]), t._v(" " + t._s(t.$t("estimateDocs")) + " ("), e("a", {
        attrs: {
          href: t.chronologyInfo && t.chronologyInfo.smeta_protocol_step && t.chronologyInfo.smeta_protocol_step.file,
          download: "download",
          target: "_blank"
        }
      }, [t._v(t._s(t.$t("downloadFile")))]), t._v(")\n                    ")])]) : t._e()]) : t._e(), t._v(" "), t.chronologyInfo && 4 != t.chronologyInfo.type ? e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.announce_contractor_step
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[6]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(6);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologyAnnounceContractorStep")))])]), t._v(" "), t.chronology[6] ? e("div", {
        staticClass: "chronology-info__body"
      }, [e("p", [e("b", [t._v(t._s(t.$t("chronologyAdDate")) + ":")]), t._v(" \n                        " + t._s(t._f("dateFormat")(t.chronologyInfo && t.chronologyInfo.announce_contractor_step && t.chronologyInfo.announce_contractor_step.date)) + "\n                    ")]), t._v(" "), e("p", [e("b", [t._v(t._s(t.$t("chronologyAdLotNumber")) + ":")]), t._v(" \n                        " + t._s(t.chronologyInfo && t.chronologyInfo.announce_contractor_step && t.chronologyInfo.announce_contractor_step.lot_number || "") + "\n                    ")])]) : t._e()]) : t._e(), t._v(" "), t.chronologyInfo && 4 != t.chronologyInfo.type && 1 == t.chronologyInfo.type ? e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.expert_openion_step
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[5]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(5);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologyExpertOpenionStep")))])]), t._v(" "), t.chronology[5] ? e("div", {
        staticClass: "chronology-info__body"
      }, [e("p", [e("b", [t._v(t._s(t.$t("chronologyEndAmount")) + ": ")]), t._v("\n                        " + t._s(t._f("filterSum")(t.chronologyInfo && t.chronologyInfo.expert_openion_step && t.chronologyInfo.expert_openion_step.expert_openion && t.chronologyInfo.expert_openion_step.expert_openion.expert_budget || 0)) + " (" + t._s(t.$t("som")) + ")\n                    ")]), t._v(" "), t.chronologyInfo && t.chronologyInfo.expert_openion_step && t.chronologyInfo.expert_openion_step.expert_openion && t.chronologyInfo.expert_openion_step.expert_openion.expert_file ? e("p", [e("b", [t._v(t._s(t.$t("reportWinsAdreska")) + ": ")]), t._v(" "), e("a", {
        attrs: {
          href: t.chronologyInfo.expert_openion_step.expert_openion.expert_file,
          download: "download",
          target: "_blank"
        }
      }, [t._v(t._s(t.$t("downloadFile")))])]) : t._e()]) : t._e()]) : t._e(), t._v(" "), t.chronologyInfo && 4 != t.chronologyInfo.type ? e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.contract_transaction_step && (t.chronologyInfo.contract_transaction_step.transactions.length || t.chronologyInfo.contract_transaction_step.contracts.length)
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[8]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(8);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologyContractTransactionStep")))])]), t._v(" "), t.chronology[8] ? e("div", {
        staticClass: "chronology-info__body"
      }, [e("a", {
        staticClass: "info",
        attrs: {
          href: "#"
        },
        on: {
          click: function click(e) {
            return e.preventDefault(), t.openScoreInfo.apply(null, arguments);
          }
        }
      }, [e("b-icon", {
        attrs: {
          icon: "info-circle",
          "font-scale": "1.2"
        }
      }), t._v(" " + t._s(t.$t("infoFinance")))], 1)]) : t._e()]) : t._e(), t._v(" "), t.chronologyInfo && 4 == t.chronologyInfo.type ? e("div", {
        staticClass: "chronology-info__item",
        "class": {
          active: t.chronologyInfo && t.chronologyInfo.reject_step
        }
      }, [e("div", {
        staticClass: "chronology-info__head",
        "class": {
          active: t.chronology[9]
        },
        on: {
          click: function click(e) {
            return t.clickChronology(9);
          }
        }
      }, [e("span", [e("i", {
        staticClass: "fa fa-check",
        attrs: {
          "aria-hidden": "true"
        }
      }), t._v(" " + t._s(t.$t("chronologyRejectStep")))])]), t._v(" "), t.chronology[9] ? e("div", {
        staticClass: "chronology-info__body"
      }, [e("p", [e("b", [t._v(t._s(t.$t("chronologyRejectReason")) + ": ")]), t._v("\n                        " + t._s(t.chronologyInfo && t.chronologyInfo.reject_step && t.chronologyInfo.reject_step.description || "") + "\n                    ")]), t._v(" "), t.chronologyInfo && t.chronologyInfo.reject_step && t.chronologyInfo.reject_step.file ? e("p", [e("b", [t._v(t._s(t.$t("reportWinsAdreska")) + ": ")]), t._v(" "), e("a", {
        attrs: {
          href: t.chronologyInfo.reject_step.file,
          download: "download",
          target: "_blank"
        }
      }, [t._v(t._s(t.$t("downloadFile")))])]) : t._e()]) : t._e()]) : t._e()])]);
    }, [], !1, null, "4607d06f", null);
    e["default"] = s.exports;
  },
  764: function _(t, e, n) {
    "use strict";

    n.r(e);
    n(42), n(16), n(1), n(3), n(13);
    var o = {
      name: "VoteConfirmCode",
      props: {
        close: {
          type: Function,
          "default": function _default() {
            return null;
          }
        },
        clickFunction: {
          type: Function,
          "default": function _default() {
            return null;
          }
        },
        title: {
          type: String,
          "default": function _default() {
            return this.$t("confirmCodeMessage");
          }
        },
        isMessage: {
          type: Boolean,
          "default": function _default() {
            return !1;
          }
        },
        token: {
          type: String,
          "default": function _default() {
            return "";
          }
        },
        phone: {
          type: String,
          "default": function _default() {
            return "";
          }
        }
      },
      data: function data() {
        return {
          isPending: !1,
          otp: "",
          warn: {
            status: !1,
            text: {
              qr: "Введен неверный код",
              uz: "Киритилган код нотўғри",
              en: "The code entered is incorrect",
              oz: "Kiritilgan kod noto'g'ri",
              ru: "Введен неверный код"
            }
          },
          timer: 179,
          isRunning: !1
        };
      },
      mounted: function mounted() {
        var t = this;
        this.isRunning = !0, setInterval(function () {
          t.timer > 0 ? t.timer-- : (clearInterval(t.timer), t.isRunning = !1);
        }, 1e3);
      },
      methods: {
        sendSmsCode: function sendSmsCode() {
          var t = this;
          this.isPending = !0;
          var e = this.phone.split(""),
              n = "";
          e.map(function (t) {
            return "+" === t || "-" === t || "(" === t || ")" === t || " " === t ? "" : t;
          }).forEach(function (t) {
            n += t;
          }), this.$store.dispatch("initiative/sendSmsCode", {
            phone: n,
            otp: this.otp,
            token: this.token,
            application: this.$route.params.id
          }).then(function (e) {
            e && e.response && 400 == e.response.status ? t.warn.status = !0 : e && 200 == e.status && t.$emit("okStatus");
          })["finally"](function () {
            t.isPending = !1;
          });
        }
      },
      computed: {
        smsTimer: function smsTimer() {
          if (this.timer > 0) {
            var t = this.timer % 60 != 0 ? Math.ceil(this.timer / 60) - 1 : Math.ceil(this.timer / 60),
                e = this.timer - 60 * t;
            return "".concat(t > 9 ? t : "0" + t, ":").concat(e > 9 ? e : "0" + e);
          }

          return "";
        }
      }
    },
        i = (n(747), n(15)),
        a = Object(i.a)(o, function () {
      var t = this,
          e = t._self._c;
      return e("div", {
        staticClass: "popup"
      }, [e("div", {
        staticClass: "popup-main"
      }, [e("div", {
        staticClass: "popup-head popup-head__login"
      }, [e("h2", {
        staticClass: "popup-head__title"
      }, [t._v(t._s(t.$t("smsVoteConfirm")))])]), t._v(" "), e("div", {
        staticClass: "popup-body"
      }, [e("form", {
        staticClass: "form"
      }, [t.warn.status ? e("p", {
        staticStyle: {
          color: "red"
        }
      }, [t._v("\n          " + t._s(t.warn.text[t.$i18n.locale]) + "\n        ")]) : t._e(), t._v(" "), e("b-form-group", {
        attrs: {
          label: t.$t("code") + ":",
          "label-for": "phone"
        }
      }, [e("b-form-input", {
        attrs: {
          id: "phone",
          type: "number"
        },
        on: {
          change: function change(e) {
            return t.$emit("change", t.phone);
          }
        },
        model: {
          value: t.otp,
          callback: function callback(e) {
            t.otp = t._n(e);
          },
          expression: "otp"
        }
      })], 1), t._v(" "), t.isRunning ? e("span", {
        staticClass: "foot-text"
      }, [t._v(t._s(t.$t("smsTime")) + ": "), e("strong", {
        staticClass: "foot-red",
        staticStyle: {
          "font-size": "17px"
        }
      }, [t._v(t._s(t.smsTimer))])]) : e("span", {
        staticClass: "foot-text foot-red"
      }, [t._v(t._s(t.$t("smsTimeClosed")))]), t._v(" "), e("b-button-group", [t.isRunning ? e("b-button", {
        attrs: {
          disabled: t.isPending
        },
        on: {
          click: function click(e) {
            return e.preventDefault(), t.sendSmsCode.apply(null, arguments);
          }
        }
      }, [t._v(t._s(t.$t("confirmation")) + " ")]) : e("b-button", {
        staticClass: "btn-close",
        on: {
          click: t.close
        }
      }, [t._v(t._s(t.$t("beginCloseBtn")))])], 1)], 1)]), t._v(" "), t.isRunning ? t._e() : e("span", {
        staticClass: "popup-main__close",
        on: {
          click: t.close
        }
      }, [e("b-icon", {
        attrs: {
          icon: "x"
        }
      })], 1)])]);
    }, [], !1, null, "39f1eba2", null);
    e["default"] = a.exports;
  },
  765: function _(t, e, n) {
    "use strict";

    n.r(e);
    n(330), n(42);
    var o = {
      name: "Vote-Phone",
      props: {
        close: {
          type: Function,
          "default": function _default() {
            return null;
          }
        },
        timer: {
          type: [String, Number],
          "default": function _default() {
            return null;
          }
        }
      },
      data: function data() {
        return {
          timeValue: null,
          time: null
        };
      },
      mounted: function mounted() {
        var t = this;
        this.timeValue = this.timer;
        var e = setInterval(function () {
          t.timeValue--, 0 == t.timeValue && (clearInterval(e), t.close());
        }, 1e3);
      },
      computed: {
        text: function text() {
          var t = Math.ceil(this.timeValue / 3600) - 1;
          t = t > 9 ? t : "0".concat(t);
          var e = Math.ceil(this.timeValue / 60) - 60 * t - 1;
          e = e > 9 ? e : "0".concat(e);
          var n = this.timeValue % 60,
              o = t + ":" + e + ":" + (n = n > 9 ? n : "0".concat(n));
          return {
            qr: "".concat(o, " keyin qayta urınıp kóriń"),
            uz: "".concat(o, " сўнг такрорий уриниб кóринг"),
            en: "Please try again after ".concat(o),
            oz: "".concat(o, " so'ng takroriy urinib kóring"),
            ru: "Повторите попытку через ".concat(o)
          };
        }
      }
    },
        i = n(15),
        a = Object(i.a)(o, function () {
      var t = this,
          e = t._self._c;
      return e("div", {
        staticClass: "popup"
      }, [e("div", {
        staticClass: "popup-main"
      }, [e("div", {
        staticClass: "popup-head popup-head__login"
      }), t._v(" "), e("div", {
        staticClass: "popup-body"
      }, [e("h4", {
        staticStyle: {
          "text-align": "center",
          "padding-top": "32px",
          "font-weight": "600"
        }
      }, [t._v("\n        " + t._s(t.text[t.$i18n.locale]) + "\n      ")])]), t._v(" "), e("span", {
        staticClass: "popup-main__close"
      }, [e("b-icon", {
        attrs: {
          icon: "x"
        },
        on: {
          click: t.close
        }
      })], 1)])]);
    }, [], !1, null, null, null);
    e["default"] = a.exports;
  },
  766: function _(t, e, n) {
    "use strict";

    n.r(e);
    var o = {
      name: "Vote-Response",
      props: {
        type: {
          type: String,
          "default": function _default() {
            return "success";
          }
        }
      },
      methods: {
        close: function close() {
          this.$emit("closeVote");
        }
      }
    },
        i = (n(749), n(15)),
        a = Object(i.a)(o, function () {
      var t = this,
          e = t._self._c;
      return e("div", {
        staticClass: "popup"
      }, [e("div", {
        staticClass: "popup-main popup-main--excitedNumber"
      }, [e("div", {
        staticClass: "popup-head popup-head__login"
      }, [e("h2", {
        staticClass: "popup-head__title",
        "class": t.type
      }, [t._v("\n        " + t._s("success" == t.type ? t.$t("voteSuccessfully") : t.$t("voteNotSuccessfully")) + "\n      ")])]), t._v(" "), e("span", {
        staticClass: "popup-main__close",
        on: {
          click: t.close
        }
      }, [e("b-icon", {
        attrs: {
          icon: "x"
        }
      })], 1)])]);
    }, [], !1, null, "57ce72d2", null);
    e["default"] = a.exports;
  },
  767: function _(t, e, n) {
    "use strict";

    n.r(e);
    n(330), n(163), n(1), n(3), n(19), n(47), n(62);
    var o = {
      data: function data() {
        return {
          activeTab: "contracts"
        };
      },
      props: {
        closeScore: {
          type: Function,
          "default": function _default() {
            return null;
          }
        },
        review_budget: {
          type: Number,
          "default": function _default() {
            return null;
          }
        },
        organization: {
          type: String,
          "default": function _default() {
            return "";
          }
        },
        money_transfer: {
          type: Object,
          "default": function _default() {
            return {};
          }
        },
        contracts: {
          type: Array,
          "default": function _default() {
            return [];
          }
        },
        transactions: {
          type: Array,
          "default": function _default() {
            return [];
          }
        }
      },
      filters: {
        filterSum: function filterSum(t) {
          t = (+t).toFixed(2);
          var e = (t += "").split(".")[0].split("").reverse(),
              n = "";
          return e.forEach(function (t, e) {
            n += (e + 1) % 3 == 0 ? t + " " : t;
          }), n.split("").reverse().join("") + (t.split(".")[1] ? "." + t.split(".")[1] : "");
        },
        dateFormat: function dateFormat(t) {
          var e = "";
          return t.includes("T") ? e = t.split("T")[0] : t.includes(" ") && (e = t.split(" ")[0]), e.split("-").reverse().join(".");
        }
      }
    },
        i = (n(751), n(15)),
        a = Object(i.a)(o, function () {
      var t = this,
          e = t._self._c;
      return e("div", {
        staticClass: "popup popup--scoreInfo"
      }, [e("div", {
        staticClass: "popup-main popup-main--scoreInfo"
      }, [e("div", {
        staticClass: "popup-head"
      }, [e("h2", {
        staticClass: "popup-head__title"
      }, [t._v("\n        " + t._s(t.organization || "") + "\n      ")])]), t._v(" "), e("div", {
        staticClass: "popup-body"
      }, [e("div", {
        staticClass: "intable"
      }, [e("div", {
        staticClass: "infotable"
      }, [e("table", [e("thead", [e("tr", [e("th", {
        attrs: {
          width: "60%"
        }
      }, [t._v(t._s(t.$t("indicatorName")))]), t._v(" "), e("th", {
        attrs: {
          width: "50px"
        }
      }, [t._v(t._s(t.$t("summaSum")))])])]), t._v(" "), e("tbody", [e("tr", [e("td", [t._v(t._s(t.$t("scoreTableTitle1")))]), t._v(" "), e("td", {
        staticClass: "sums"
      }, [t._v(t._s(t._f("filterSum")(t.review_budget || "")))])]), t._v(" "), e("tr", [e("td", [t._v(t._s(t.$t("scoreTableTitle2")))]), t._v(" "), e("td", {
        staticClass: "sums"
      }, [t._v(t._s(t._f("filterSum")(t.money_transfer && t.money_transfer.financed || 0)))])]), t._v(" "), e("tr", [e("td", [t._v(t._s(t.$t("scoreTableTitle3")))]), t._v(" "), e("td", {
        staticClass: "sums"
      }, [t._v(t._s(t._f("filterSum")(t.money_transfer && t.money_transfer.cost || 0)))])]), t._v(" "), e("tr", [e("td", [t._v(t._s(t.$t("scoreTableTitle4")))]), t._v(" "), e("td", {
        staticClass: "sums"
      }, [t._v(t._s(t._f("filterSum")(t.money_transfer && t.money_transfer.residue || 0)))])])])])]), t._v(" "), e("br"), t._v(" "), e("div", {
        staticClass: "scoreInfo-tabs"
      }, [e("button", {
        staticClass: "btn scoreInfo-tab",
        "class": {
          active: "contracts" == t.activeTab
        },
        on: {
          click: function click(e) {
            t.activeTab = "contracts";
          }
        }
      }, [t._v(t._s(t.$t("registeredContracts")))]), t._v(" "), e("button", {
        staticClass: "btn scoreInfo-tab",
        "class": {
          active: "transactions" == t.activeTab
        },
        on: {
          click: function click(e) {
            t.activeTab = "transactions";
          }
        }
      }, [t._v(t._s(t.$t("paymentOrders")))])]), t._v(" "), "contracts" == t.activeTab ? e("div", {
        staticClass: "infotable"
      }, [e("table", [e("thead", [e("tr", [e("th", {
        attrs: {
          width: "200"
        }
      }, [t._v(t._s(t.$t("contractsTitle1")))]), t._v(" "), e("th", [t._v(t._s(t.$t("contractsTitle2")))]), t._v(" "), e("th", [t._v(t._s(t.$t("contractsTitle3")))]), t._v(" "), e("th", [t._v(t._s(t.$t("contractsTitle4")))]), t._v(" "), e("th", {
        attrs: {
          width: "400"
        }
      }, [t._v(t._s(t.$t("contractsTitle5")))]), t._v(" "), e("th", [t._v(t._s(t.$t("contractsTitle6")))])])]), t._v(" "), t.contracts && t.contracts.length ? e("tbody", t._l(t.contracts, function (n, o) {
        return e("tr", {
          key: o
        }, [e("td", [t._v(t._s(n.supplier_name || ""))]), t._v(" "), e("td", [t._v(t._s(n.inn || ""))]), t._v(" "), e("td", [e("span", {
          staticClass: "nowrap"
        }, [t._v(t._s(n.contract_number || ""))])]), t._v(" "), e("td", [e("span", {
          staticClass: "nowrap"
        }, [t._v(t._s(t._f("filterSum")(n.amount || "")))])]), t._v(" "), e("td", [t._v(t._s(n.detail || ""))]), t._v(" "), e("td", [e("span", {
          staticClass: "nowrap"
        }, [t._v(t._s(t._f("dateFormat")(n.register_date || "")))])])]);
      }), 0) : e("div", [e("p", [t._v(t._s(t.$t("infoNotFound")))])])])]) : t._e(), t._v(" "), "transactions" == t.activeTab ? e("div", {
        staticClass: "infotable"
      }, [e("table", [e("thead", [e("tr", [e("th", [t._v(t._s(t.$t("transactionsTitle1")))]), t._v(" "), e("th", [t._v(t._s(t.$t("transactionsTitle2")))]), t._v(" "), e("th", {
        attrs: {
          width: "200"
        }
      }, [t._v(t._s(t.$t("transactionsTitle3")))]), t._v(" "), e("th", [t._v(t._s(t.$t("transactionsTitle4")))]), t._v(" "), e("th", [t._v(t._s(t.$t("transactionsTitle5")))])])]), t._v(" "), t.transactions && t.transactions.length ? e("tbody", t._l(t.transactions, function (n, o) {
        return e("tr", {
          key: o
        }, [e("td", [t._v(t._s(n.document_number || ""))]), t._v(" "), e("td", [e("span", {
          staticClass: "nowrap"
        }, [t._v(t._s(t._f("filterSum")(n.amount || "")))])]), t._v(" "), e("td", [t._v(t._s(n.detail || ""))]), t._v(" "), e("td", [e("span", {
          staticClass: "nowrap"
        }, [t._v(t._s(t._f("dateFormat")(n.transfered_date || "")))])]), t._v(" "), e("td", [t._v(t._s(n.article || ""))])]);
      }), 0) : e("div", [e("p", [t._v(t._s(t.$t("infoNotFound")))])])])]) : t._e()])]), t._v(" "), e("span", {
        staticClass: "popup-main__close",
        on: {
          click: t.closeScore
        }
      }, [e("b-icon", {
        attrs: {
          icon: "x"
        }
      })], 1)])]);
    }, [], !1, null, "29841440", null);
    e["default"] = a.exports;
  },
  806: function _(t, e, n) {
    t.exports = n.p + "img/voteanim.cb88a27.gif";
  },
  865: function _(t, e, n) {
    var o = n(1142);
    o.__esModule && (o = o["default"]), "string" == typeof o && (o = [[t.i, o, ""]]), o.locals && (t.exports = o.locals);
    (0, n(37)["default"])("435013fa", o, !0, {
      sourceMap: !1
    });
  }
}]);