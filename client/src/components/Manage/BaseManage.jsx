import React, { Component } from "react";
import { Route, Redirect } from "react-router-dom";
import ReactLoading from "react-loading";

function isLogged() {
  var status = false;
  if (sessionStorage.getItem("OL_TOKEN")) {
    status = true;
  }
  return status;
}

export const PrivateRoute = ({ component: Component, ...rest }) => (
  <Route
    {...rest}
    render={props => {
      return isLogged() ? (
        <Component {...props} />
      ) : (
        <Redirect
          to={{
            pathname: "/manage/login",
            state: { from: props.location }
          }}
        />
      );
    }}
  />
);

export const Breadcrumb = ({ home, manage }) => {
  return (
    <div className="Breadcrumb">
      <p>
        <span>{home}</span>
        <span>/</span>
        <span>{manage}</span>
      </p>
    </div>
  );
};

export function TableWrap(props) {
  return (
    <table className="table table-management">
      <thead>
        <tr>
          {props.columns.map((col, index) => {
            return <th key={index}>{col}</th>;
          })}
        </tr>
      </thead>
      <tbody>{props.children}</tbody>
    </table>
  );
}
export function HeaderTable(props) {
  return (
    <div className="table_title">
      <div className="title-box">
        <div className="title-add">{props.children}</div>
      </div>
    </div>
  );
}
export function ContentManage(props) {
  return (
    <div className="content-table">
      <div className="table-box">{props.children}</div>
    </div>
  );
}
export function ModalBackground(props) {
  return (
    <div className="modal-manage">
      <div className="modal_backdrop show" onClick={() => props.onClick()} />
      <div className="modal-main">
        <div className="modal_box" style={{ width: "" + props.width + "px" }}>
          <div className="modal_header row-title">
            <div className="modal_title">
              <p>{props.title}</p>
            </div>
            <div className="modal_close">
              <span onClick={() => props.onClick()}>Đóng</span>
            </div>
          </div>
          <div className="modal-container">
            <div className="modal_table">{props.children}</div>
          </div>
        </div>
      </div>
    </div>
  );
}
export const Loading = () => {
  return (
    <div className="ReactLoading">
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
export const Input = ({ placeholder, name, type }) => {
  return (
    <div className="form-group mag15">
      <input
        type={type}
        className="form-control"
        placeholder={placeholder}
        name={name}
      />
    </div>
  );
};
export const Select = ({name,data}) => {
  return (
    <div className="form-group mag15">
      <select className="form-control" name={name}>
      {data?data.map((subject,index) =>{
        return (
          <option value={subject.SUBID} key={index}>{subject.SUBTEXT}</option>
        )
      }):""}
      </select>
    </div>
  );
};
export function ItemQuestion(props) {
  return (
    <div className="number-que">
      <div className="box-question">
        <div className="content-q-file">
          <div className="name-q-file ">
            <p>
              <span>Câu {props.index}: </span>
              {props.title}
            </p>
          </div>
          <div className="answer-q-file padL10">
            <table>
              <tbody>{props.children}</tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}
export const ButtonReadFile = ({ onChangeReadFile }) => {
  return (
    <div className="form-group mag15">
      <input
        type="file"
        className="form-control"
        name="readfile"
        onChange={onChangeReadFile}
      />
    </div>
  );
};
export function ButtonPrimary(props) {
  return (
    <button className="btn btn_primary" type="submit" onClick={props.onClick}>
      {props.children}
    </button>
  );
}
