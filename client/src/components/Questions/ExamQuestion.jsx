import React, { Component } from "react";
import { ReactDOM } from "react-dom";
import { connect } from "react-redux";
import { updateStateData } from "./../../actions/index";
import {
  Button,
  Container,
  Row,
  Col,
  Breadcrumb,
  BreadcrumbItem
} from "react-bootstrap";
import { Redirect, Prompt, Link } from "react-router-dom";
import { API } from "./../../API/API";
import axios from "axios";
import ReactLoading from "react-loading";
const ExamContext = React.createContext();

export const SecBreadcrumb = ({ Exam }) => {
  const { name } = Exam;
  return (
    <div className="ol-breadcrumb">
      <Container>
        <Breadcrumb className="breadcrumb__content">
          <BreadcrumbItem href="/home">Trang chủ</BreadcrumbItem>
          <BreadcrumbItem active>{name}</BreadcrumbItem>
        </Breadcrumb>
      </Container>
    </div>
  );
};

class Timer extends Component {
  constructor() {
    super();
    this.state = {
      count: 2,
      isCheck: false
    };
  }
  componentDidMount() {
    const { time } = this.props;
    var seconds = time * 60;
    // var seconds = 5;
    this.setState({
      count: seconds
    });
    this.doIntervalChange();
  }
  componentWillUnmount() {
    clearInterval(this.myInterval);
  }
  doIntervalChange = () => {
    if (this.state.count > 0) {
      this.myInterval = setInterval(
        () =>
          this.setState({
            count: this.state.count - 1
          }),
        1000
      );
    }
  };
  minutes = () => {
    var min = Math.round((this.state.count - 30) / 60);
    return ("0" + min).slice(-2);
  };
  seconds = () => {
    return ("0" + (this.state.count % 60)).slice(-2);
  };
  SendResult = () => {
    this.props.IsCheck();
  };
  render() {
    const { count } = this.state;
    if (count === 0) {
      clearInterval(this.myInterval);
      this.SendResult();
    }
    return (
      <div className="exam-time">
        <div className="get-time">
          <p>
            <span>{this.minutes()}</span>
            <span className="haicham">:</span>
            <span>{this.seconds()}</span>
          </p>
        </div>
      </div>
    );
  }
}
export const ItemShortcut = ({ number, item }) => {
  return (
    <React.Fragment>
      <ExamContext.Consumer>
        {({ onChangeMove }) => (
          <div
            onClick={() => onChangeMove(item.ID_QUE)}
            className={
              item.choose == true
                ? "item-question-shortcut answered"
                : "item-question-shortcut"
            }
          >
            {number + 1}
          </div>
        )}
      </ExamContext.Consumer>
    </React.Fragment>
  );
};

export const ColumnItemLeft = () => {
  return (
    <div className="left-menu-exam">
      <ExamContext.Consumer>
        {({ mainState, Exam, Finish, IsCheck }) => (
          <div
            className={
              mainState.scroll >= 100
                ? "content-menu-exam scroll-top"
                : "content-menu-exam"
            }
          >
            <React.Fragment>
              {Exam.time ? <Timer time={Exam.time} IsCheck={IsCheck} /> : ""}
              <div className="list-question-shortcut over-question">
                {Exam.List
                  ? Exam.List.map((item, index) => {
                      return (
                        <ItemShortcut item={item} key={index} number={index} />
                      );
                    })
                  : ""}
              </div>
              <div className="wrap-finish text-center">
                {Exam.List ? (
                  <button className="btn btn-danger" onClick={Finish}>
                    Finish
                  </button>
                ) : (
                  ""
                )}
              </div>
            </React.Fragment>
          </div>
        )}
      </ExamContext.Consumer>
    </div>
  );
};

export const RowItemAnswer = ({ Answer }) => {
  return (
    <div className="answer-q">
      <table>
        <tbody>
          {Answer
            ? Answer.map((answer, index) => {
                return <ItemAnswerChildren key={index} answer={answer} />;
              })
            : ""}
        </tbody>
      </table>
    </div>
  );
};
export const ItemAnswerChildren = ({ answer }) => {
  const { ID_ANS, ID_QUE } = answer;
  return (
    <React.Fragment>
      <ExamContext.Consumer>
        {({ onChangeQuestion }) => (
          <React.Fragment>
            {answer ? (
              <tr>
                <td>
                  <input
                    type="radio"
                    className="option_que radio_que"
                    value={ID_ANS}
                    name={ID_QUE}
                    onChange={() => onChangeQuestion(ID_QUE)}
                  />
                </td>
                <td>
                  <p>{answer.ANS_TEXT}</p>
                </td>
              </tr>
            ) : (
              ""
            )}
          </React.Fragment>
        )}
      </ExamContext.Consumer>
    </React.Fragment>
  );
};
export const ItemQuetion = ({ item, index }) => {
  const { Answer } = item;
  return (
    <div className="number-q" id={item.ID_QUE}>
      <div className="box-question">
        <div className="content-q">
          <RowItemQuestion itemQue={item} index={index} />
          <RowItemAnswer Answer={Answer} />
        </div>
      </div>
    </div>
  );
};
export const RowItemQuestion = ({ itemQue, index }) => {
  return (
    <div className="name-q">
      <div className="info-q">
        <h3>Câu {index}</h3>
      </div>
      <p>{itemQue ? itemQue.QUE_TEXT : ""}</p>
    </div>
  );
};
export const ColumnItemRight = () => {
  return (
    <div className="box-wrapper">
      <div className="the-questions">
        <ExamContext.Consumer>
          {({ Exam }) =>
            Exam.List
              ? Exam.List.map((item, index) => {
                  return (
                    <ItemQuetion
                      key={item.ID_QUE}
                      index={index + 1}
                      item={item}
                    />
                  );
                })
              : ""
          }
        </ExamContext.Consumer>
      </div>
    </div>
  );
};

export const MainExamQuestion = () => {
  return (
    <div className="container">
      <div className="exam-grid page__wrapper choose_exam">
        <ColumnItemLeft />
        <ColumnItemRight />
      </div>
    </div>
  );
};
export const Loading = () => {
  return (
    <div className="show_ReactLoading">
      <div className="modal_backdrop show" />
      <div className="show_loading">
        <ReactLoading
          className="loading_inline"
          type={"spinningBubbles"}
          color="#fff"
        />
      </div>
    </div>
  );
};
class ExamQuestion extends Component {
  constructor() {
    super();
    this.state = {
      idExam: "",
      loading: false,
      payload: false,
      isNext: true,
      scroll: 0
    };
  }
  componentDidMount() {
    const { location } = this.props.location;
    const { id } = this.props.mainState.Exam;
    let params = new URLSearchParams(location.search);
    let idExam = params.get("id");
    let idux = params.get("idux");

    let data = {
      id: idExam,
      idux: idux
    };
    if (id == "") {
      this.GetExamRequestId(data);
    }

    this.setState({
      idExam,
      idux
    });
  }

  GetExamRequestId = data => {
    axios({
      method: "POST",
      url: `${API}/GetExamQuestionId`,
      data: data
    })
      .then(json => {
        const { status, Questions, data } = json.data;
        console.log("success");
        if (status == "success") {
          this.props.dispatch(
            updateStateData({
              ...this.props.mainState,
              Exam: {
                id: data.IDEXAM,
                name: data.EXAMTEXT,
                time: data.EXTIME,
                random: data.RANDOMEXAM,
                List: Questions
              }
            })
          );
        }
      })
      .catch(err => {
        console.error(err);
      });
  };
  Finish = e => {
    e.preventDefault();
    if (window.confirm("Bạn đã chắn chắn muốn nộp bài không?")) {
      this.setState({
        loading: true
      });
      this.GetAnswerUserId();
    }
  };
  GetAnswerUserId() {
    let inputs = document.getElementsByTagName("input");
    let result = [];
    if (inputs.length > 0) {
      for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].type === "radio" && inputs[i].checked === true) {
          result.push({
            idAns: inputs[i].value,
            idQue: inputs[i].name
          });
        }
      }
    }
    var currentDate = new Date();
    var timeNow = currentDate.getHours() + ":" + currentDate.getMinutes();
    var token = localStorage.getItem("token");
    var Data = {
      idExam: this.state.idExam,
      idux: this.state.idux,
      token: token,
      timeNow: timeNow,
      questions: result
    };
    axios({
      method: "POST",
      url: `${API}/GetQuestionUserId`,
      data: Data
    })
      .then(json => {
        const { data } = json;
        if (data.error) {
          console.log(data);
        } else {
          this.props.dispatch(
            updateStateData({
              ...this.props.mainState,
              scroll: 0,
              Exam: {
                id: "",
                name: "",
                time: "",
                random: "",
                List: []
              }
            })
          );
          this.setState({
            isNext: false
          });
        }
      })
      .catch(err => {
        console.error(err);
      });
  }
  IsCheck = () => {
    this.GetAnswerUserId();
  };
  onChangeQuestion = id => {
    const { Exam } = this.props.mainState;
    var index = Exam.List.map(function(item) {
      return item.ID_QUE;
    }).indexOf(id);
    Exam.List[index].choose = true;
    this.props.dispatch(
      updateStateData({
        ...this.props.mainState,
        Exam: Exam
      })
    );
  };
  onChangeMove = id => {
    var top = document.getElementById(""+id+"");
    window.scrollTo(0,top.offsetTop);
  };
  render() {
    const { Exam } = this.props.mainState;
    const { loading, isNext, idux } = this.state;
    if (isNext === false) {
      return (
        <Redirect to={{ pathname: `/result-test`, search: `?id=${idux}` }} />
      );
    }
    return (
      <div className="question-test online-test">
        <SecBreadcrumb Exam={Exam} />
        <ExamContext.Provider
          value={{
            dispatch: this.props.dispatch,
            mainState: this.props.mainState,
            Exam: Exam ? Exam : "",
            Finish: this.Finish,
            IsCheck: this.IsCheck,
            onChangeQuestion: id => this.onChangeQuestion(id),
            onChangeMove: id => this.onChangeMove(id)
          }}
        >
          <MainExamQuestion />
        </ExamContext.Provider>
        {loading ? <Loading /> : ""}
        <Prompt
          when={isNext}
          message={() => "Bạn đã chắc chắn muốn thoát không?"}
        />
      </div>
    );
  }
}
export default connect(state => {
  return {
    mainState: state.updateStateData
  };
})(ExamQuestion);
